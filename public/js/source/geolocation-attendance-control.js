/**
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

document.addEventListener('DOMContentLoaded', function ()
{
  Vue.component('gmap-map', VueGoogleMaps.Map)

  Vue.component('VueGoogleMapsPlacesAggregator', VueGoogleMapsPlacesAggregator.VueGoogleMapsPlacesAggregator)

  Vue.use(VueGoogleMaps, {
    load: {
      key: geolocationAttendanceControl.google_api_key
    },
  })
  new Vue({
    el: '#app',
    data: vm => ({
      selectedActivity: null,
      selectedCentre: null,
      selectedSchedule: null,
      schedulesList: [],
      centres: [],
      activities: [],
      activitiesList: [],
      position: null,
      errorMessage: null,
      successMessage: null,
      positionReceived: null,
      selected: [],
      search: '',
      headerTitle: 'Asistencias',
      place: null,
      centre: '',
      activity: '',
      startDateSelector: false,
      endDateSelector: false,
      startDate: '',
      endDate: '',
      startTime: false,
      start_hour: null,
      endTime: false,
      end_hour: null,
      pagination: {
        descending: true,
        page: 1,
        rowsPerPage: 10,
        sortBy: 'id'
      },
      placeholder: 'Dirección',
      className: '',
      id: '',
      value: '',
      options: {
        componentRestrictions: {
          country: 'es'
        }
      },
      currentIndex: null,
      zoom: 10,
      center: {
        lat: 37.392529,
        lng: -5.994072
      },
      geolocationOptions: {
        enableHighAccuracy: false,
        timeout: Infinity,
        maximumAge: 0
      },
      width: '100%',
      page: 'attendances',
      height: '400px',
      maxHeight: '600px',
      maxWidth: '400px',
      markers: [],
      toolbarTitle: 'Asistencias',
      toolbarColor: 'indigo',
      formatDate: 'DD [de] MMMM [de] YYYY',
      formatHour: 'hh:mm',
      defaultDateOrder: 'DEC',
      defaultHourOrder: 'DEC',
      locale: 'es',
      dialog: false,
      dialogConfirm: false,
      dialogTitle: '',
      dialogSubtitle: '',
      dateSelectorDialog: true,
      editedIndex: -1
    }),

    mounted()
    {
      setTimeout(function ()
      {
        document.querySelector('.opacity-on').classList.add('opacity-off')
      }, 100);

      this.getCurrentPosition(this.geolocationOptions).then((position) =>
      {
        if (position.coords)
        {
          this.position = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          }
          this.selectActivities()
          this.save()
          console.log(this.position)
        } else
        {
          this.errorMessage = 'NO PUEDES CONTINUAR: LA GEOLOCALIZACIÓN NO ES COMPATIBLE CON TU NAVEGADOR.'
        }
      }).catch((err) =>
      {
        console.error('ERR', err.message)
        var msg = null;
        switch (err.code)
        {
          case err.PERMISSION_DENIED:
            this.errorMessage = "NO PUEDES CONTINUAR: DENEGASTE EL ACCESO A TU UBICACIÓN."
            break;
          case err.POSITION_UNAVAILABLE:
            this.errorMessage = "NO PUEDES CONTINUAR: LA INFORMACIÓN SOBRE TU UBICACIÓN NO ESTÁ DISPONIBLE."
            break;
          case err.TIMEOUT:
            this.errorMessage = "NO PUEDES CONTINUAR: LA SOLICITUD PARA OBTENER TU UBICACIÓN HA SUPERADO EL TIEMPO DE ESPERA."
            break;
          case err.UNKNOWN_ERROR:
            this.errorMessage = "NO PUEDES CONTINUAR: SE HA PRODUCIDO UN ERROR DESCONOCIDO."
            break;
        }
      })
    },

    methods: {

      getLoaderPointsImage()
      {
        return geolocationAttendanceControl.loader_gif
      },

      getLoaderImage()
      {
        return geolocationAttendanceControl.location_loader_gif
      },

      getErrorImage()
      {
        return geolocationAttendanceControl.location_error_gif
      },

      getCurrentPosition(options)
      {
        if (navigator.geolocation)
        {
          return new Promise(function (resolve, reject)
          {
            navigator.geolocation.getCurrentPosition(resolve, reject, options)
          })
        } else
        {
          return new Promise(
            resolve => resolve({})
          )
        }
      },

      selectActivities()
      {
        this.activities = []
        this.request('post', 'select_activities').then((response) =>
        {
          this.activities = response
          this.activities.forEach((item, index) =>
          {
            let centreIndex = this.centres.findIndex(el => el.centre === item.centre)
            if (centreIndex === -1)
            {
              this.centres.push({
                id: item.centre_id,
                centre: item.centre
              })
            }
          })
          console.log(response)
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      onChangeCentre()
      {
        this.activitiesList = []
        this.schedulesList = []
        this.selectedActivity = null
        this.selectedSchedule = null
        this.activities.forEach((item, index) =>
        {
          let activityIndex = this.activitiesList.findIndex(el => el.activity === item.activity_id)
          if (activityIndex === -1 && item.centre_id === this.selectedCentre.id)
          {
            this.activitiesList.push({
              id: item.id,
              activity: item.activity
            })
          }
        })
      },

      onChangeActivity()
      {
        this.schedulesList = []
        this.selectedSchedule = null
        this.activities.forEach((item, index) =>
        {
          if (item.centre_id === this.selectedCentre.id && item.activity === this.selectedActivity.activity)
          {
            this.schedulesList.push({
              id: item.id,
              schedule: item.start_hour + ' - ' + item.end_hour
            })
          }
        })
      },

      request(type, action, params)
      {
        var data = new URLSearchParams()
        params = params || {}
        data.append('action', action)
        data.append('nonce', geolocationAttendanceControl.nonce)
        for (var key in params)
        {
          data.append(key, params[key])
          console.log(key, params[key])
        }
        return new Promise((resolve, reject) =>
        {
          return axios({
            method: type,
            url: geolocationAttendanceControl.ajax_url,
            data: data
          })
            .then(response =>
            {
              resolve(response.data)
            })
            .catch(error =>
            {
              console.log(error)
              reject(error)
            })
        })
      },

      selectAttendances() { },

      save()
      {
        this.addMarker({
          position: this.position,
          clickable: false,
          infoText: '',
        })
        this.centerMap(this.position)
        this.close()
        this.zoomMap(15)

      },

      setAttendance(action)
      {
        var data = {
          latitude: this.position.lat,
          longitude: this.position.lng,
          activity_id: this.selectedSchedule.id,
          attendance_action: action
        }
        this.request('post', 'set_attendance', data).then((response) =>
        {
          console.log(response)
          if (response)
          {
            this.positionReceived = true
            this.markers = []
            response.infoText = this.getTemplate(
              response.hour,
              this.position.lat,
              this.position.lng,
              response.address
            )
            response.position = this.position
            response.hour = response.hour
            response.value = false
            response.clickable = true

            this.addMarker(response)
            this.centerMap(this.position)
            this.zoomMap(15)
            this.goToMarker(response)

            if (response.error_msg)
            {
              this.errorMessage = response.error_msg
            } else if (response.success_msg)
            {
              this.successMessage = response.success_msg
            }
          } else
          {
            this.errorMessage = "NO PUEDES CONTINUAR: SE HA PRODUCIDO UN ERROR AL CONECTAR CON LA BASE DE DATOS."
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      getTemplate(hour, latitude, longitude, address)
      {
        return `
          <div id="the-contents">
            <div class="gac-heading">
            <h3 style="text-align: center; margin-top: 2px;" class="gac-second-heading">${latitude}, ${longitude}</span></h3>
            </div>
            <div id="bodyContent">
              <p style="text-align: center; margin-bottom: 0px;">${address}</p>
            </div>
          </div>
        `
      },

      deleteAttendance()
      {
        var data = {
          id: this.markers[this.currentIndex].id
        }
        this.request('post', 'delete_attendance', data).then((response) =>
        {
          if (response)
          {
            this.markers.splice(this.currentIndex, 1)
            this.currentIndex = null
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      onOpenMarker(marker, idx)
      {
        console.info('onOpenMarker', marker, idx)
      },

      onCloseMarker(marker, idx)
      {
        console.info('onCloseMarker', marker, idx)
      },

      onCLickMarker(marker, idx)
      {
        console.info('onCLickMarker', marker, idx)
      },

      onKeyUp(event)
      {
        console.info('onKeyUp', event)
      },

      onKeyPress(event)
      {
        console.info('onKeyPress', event)
      },

      onFocus()
      {
        console.info('onFocus')
      },

      onBlur()
      {
        console.info('onBlur')
      },

      onChange()
      {
        console.info('onChange')
      },

      onPlaceChanged(place)
      {
        this.place = place
        console.info('onPlaceChanged', place)
      },

      clear()
      {
        this.$refs.autocomplete.clear()
      },

      deleteItemAsk(item)
      {
        this.dialogTitle = 'Eliminar registro'
        this.dialogSubtitle = '¿Eliminar el registro ' + item.id + '?'
        this.dialogConfirm = true
        this.currentIndex = this.markers.indexOf(item)
      },

      deleteItems()
      {
        this.dialogTitle = ''
        this.dialogSubtitle = ''
        this.dialogConfirm = false
        if (this.currentIndex !== null)
        {
          this.deleteAttendance()
        } else
        {
          this.deleteSelectedItems()
        }
      },

      deleteItemsAsk()
      {
        this.dialogTitle = 'Eliminar registros'
        this.dialogSubtitle = '¿Eliminar los registros seleccionados?'
        this.dialogConfirm = true
      },

      deleteSelectedItems()
      {
        this.selected.forEach((item) =>
        {
          this.currentIndex = this.markers.indexOf(item)
          this.deleteAttendance()
        })
        this.selected = []
      },

      close()
      {
        this.dialog = false
        this.resetForm()
      },

      save()
      {
        this.addMarker({
          position: this.position,
          clickable: false,
          infoText: '',
        })
        this.centerMap(this.position)
        this.close()
        this.zoomMap(15)

      },

      goToMarker(marker)
      {
        this.$refs.google_maps_places_aggregator.infoWinOpen = false
        this.$refs.google_maps_places_aggregator.infoWindowPos = marker.position
        this.$refs.google_maps_places_aggregator.infoOptions.content = marker.infoText
        this.$refs.google_maps_places_aggregator.infoWinOpen = true
        this.centerMap(marker.position)
        this.zoomMap(15)
      },

      addMarker(marker)
      {
        this.markers.push(marker)
      },

      centerMap(position)
      {
        this.center = position
      },

      zoomMap(value)
      {
        this.zoom = value
      },

      resetForm()
      {
        this.centre = ''
        this.activity = ''
        this.start_hour = null
        this.end_hour = null
        this.place = null
      },

      saveDate(type)
      {
        if (type === 'start')
        {
          this.$refs.start_date_selector.save(this.startDate)
        } else
        {
          this.$refs.end_date_selector.save(this.endDate)
        }
        this.selectAttendances()
        // this.$vuetify.goTo('#gac-datatable')
      }
    },

    watch:
    {
      dialog(val)
      {
        val || this.close()
      }
    },

    computed:
    {
      formattedStartDate()
      {
        return moment(this.startDate).format('DD-MM-YY')
      },
      formattedEndDate()
      {
        return moment(this.endDate).format('DD-MM-YY')
      },
      valid: function ()
      {
        return this.centre !== '' &&
          this.activity !== '' &&
          this.start_hour &&
          this.end_hour &&
          this.place
      },
      formTitle()
      {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      }
    }
  })
})