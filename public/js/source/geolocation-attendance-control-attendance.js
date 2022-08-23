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
      key: geolocationAttendanceControlAttendance.google_api_key
    },
  })
  new Vue({
    el: '#app',
    data: vm => ({
      selected: [],
      search: '',
      headerTitle: 'Asistencias',
      place: null,
      centre: '',
      activity: '',
      startDateSelector: false,
      endDateSelector: false,
      startDate: moment().toISOString().substr(0, 10),
      endDate: moment().toISOString().substr(0, 10),
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
      currentID: null,
      zoom: 10,
      center: {
        lat: 37.392529,
        lng: -5.994072
      },
      width: '100%',
      page: 'attendances',
      height: '400px',
      maxHeight: '400px',
      maxWidth: '100%',
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
      headers: [
        {
          text: 'ID',
          align: 'left',
          value: 'id',
          sortable: true
        },
        {
          text: 'Acción',
          align: 'center',
          value: 'action',
          sortable: false
        },
        { text: 'Hora', value: 'hour', sortable: false, align: 'center' },
        { text: 'Nombre', value: 'user_fullname', sortable: true, align: 'right' },
        { text: 'Centro', value: 'centre_name', sortable: true, align: 'right' },
        { text: 'Actividad', value: 'activity_name', sortable: true, align: 'right' },
        { text: 'Fecha', value: 'date', sortable: true, align: 'right' },
        { text: 'Acciones', value: 'centre', sortable: false, align: 'center' }
      ],
      editedIndex: -1
    }),

    mounted()
    {
      this.selectAttendances()

    },

    methods: {
      getTemplate(centre, activity, start_hour, end_hour, address)
      {
        return `
          <div id="content">
            <div class="gac-heading">
              <h1 class="gac-first-heading">${centre}</h1>
              <h3 class="gac-second-heading">${activity} - ${start_hour} - ${end_hour}</span></h3>
              
            </div>
            <div id="bodyContent">
              <p>${address}</p>
            </div>
          </div>
        `
      },

      request(type, action, params)
      {
        var data = new URLSearchParams()
        params = params || {}
        data.append('action', action)
        data.append('nonce', geolocationAttendanceControlAttendance.nonce)
        for (var key in params)
        {
          data.append(key, params[key])
        }
        return new Promise((resolve, reject) =>
        {
          return axios({
            method: type,
            url: geolocationAttendanceControlAttendance.ajax_url,
            data: data
          })
            .then(response =>
            {
              console.log(response)
              resolve(response.data)
            })
            .catch(error =>
            {
              console.log(error)
              reject(error)
            })
        })
      },

      selectAttendances()
      {
        var data = {
          start_date: moment(this.startDate).format('YYYY-MM-DD'),
          end_date: moment(this.endDate).format('YYYY-MM-DD')
        }

        this.request('post', 'select_attendances', data).then((response) =>
        {
          console.log(response)
          this.markers = []
          response.forEach((marker) =>
          {
            marker.infoText = this.getTemplate(
              marker.user_fullname,
              marker.action,
              moment(marker.date).format('HH:mm'),
              marker.address
            )
            marker.position = {
              lat: parseFloat(marker.latitude),
              lng: parseFloat(marker.longitude)
            }
            marker.hour = moment(marker.date).format('HH:mm')
            marker.fullhour = moment(marker.date).format('HH:mm:ss')
            marker.date = moment(marker.date).format('DD-M-YYYY')
            marker.value = false
            marker.clickable = true

            this.addMarker(marker)

          })
          document.querySelector('.opacity-on').classList.add('opacity-off')
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      getTemplate(fullname, action, hour, address)
      {
        address = address === 'Desconocida' ? 'Dirección desconocida' : address
        return `
          <div id="content">
            <div class="gac-heading">
              <h1 class="gac-first-heading">${fullname}</h1>
              <h3 class="gac-second-heading">${action} a las ${hour}.</span></h3>
              
            </div>
            <div id="bodyContent">
              <p>${address}</p>
            </div>
          </div>
        `
      },

      insertCentre()
      {
        var data = {
          centre: this.centre,
          activity: this.activity,
          start_hour: this.start_hour,
          end_hour: this.end_hour,
          latitude: this.place.location.lat,
          longitude: this.place.location.lng,
          address: this.place.formatted_address,
          place_id: this.place.place_id
        }
        this.request('post', 'insert_centre', data).then((response) =>
        {
          if (response)
          {
            this.save(response)
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      createCSV(items, betweenDates)
      {
        var markers = []
        var csv = ''

        items.forEach((item) =>
        {
          markers.push({
            ID: item.id.replace(/,/g, '').trim(),
            NOMBRE: item.user_first_name.replace(/,/g, '').trim(),
            APELLIDOS: item.user_last_name.replace(/,/g, '').trim(),
            DNI: item.user_dni.replace(/,/g, '').trim(),
            IP: item.user_ip ? item.user_ip.replace(/,/g, '').trim() : '',
            CENTRO: item.centre_name.replace(/,/g, '').trim(),
            ACTIVIDAD: item.activity_name.replace(/,/g, '').trim(),
            DIRECCIÓN: item.centre_address.replace(/,/g, '').trim(),
            ACCIÓN: item.action.replace(/,/g, '').trim(),
            HORA: item.hour.replace(/,/g, '').trim(),
            FECHA: item.date.replace(/,/g, '').trim(),
            'UBICACIÓN VÁLIDA': parseInt(item.action_status) ? 'Sí' : 'No',
            'HORARIO VÁLIDO': parseInt(item.hour_status) ? 'Sí' : 'No',
            FECHA: item.date.replace(/,/g, '').trim(),
            LATITUD: item.latitude.replace(/,/g, '').trim(),
            LONGITUD: item.longitude.replace(/,/g, '').trim()
          })
        })

        var json = markers;

        var csv = this.JSON2CSV(json);
        var downloadLink = document.createElement("a");
        var blob = new Blob(["\ufeff", csv]);
        var url = URL.createObjectURL(blob);
        downloadLink.href = url;
        downloadLink.download = betweenDates ? 'registros-desde-el-' + moment(this.startDate).format('DD-MM-YY') + '-hasta-' + moment(this.endDate).format('DD-MM-YY') + '.csv' : 'registros-seleccionados-' + moment(this.startDate).format('DD-MM-YY') + '.csv';

        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);

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

      JSON2CSV(objArray)
      {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';
        var line = '';
        var head = array[0];

        for (var index in array[0])
        {
          line += index + ',';
        }
        line = line.slice(0, -1);
        str += line + '\r\n';
        for (var i = 0; i < array.length; i++)
        {
          var line = '';
          for (var index in array[i])
          {
            line += array[i][index].replace(/,/g, '').trim() + ',';
          }
          line = line.slice(0, -1);
          str += line + '\r\n';
        }
        return str;
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
        this.currentID = item.id
      },

      deleteItems()
      {
        this.dialogTitle = ''
        this.dialogSubtitle = ''
        this.dialogConfirm = false
        if (this.currentID !== null)
        {
          this.deleteAttendance(this.currentID)
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
          this.deleteAttendance(item.id)
        })
        this.selected = []
      },

      deleteAttendance(currentID)
      {
        var data = {
          id: currentID
        }
        this.request('post', 'delete_attendance', data).then((response) =>
        {
          if (response)
          {
            var index = this.markers.findIndex(item => item.id === currentID)
            this.markers.splice(index, 1)
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      close()
      {
        this.dialog = false
        this.resetForm()
      },

      save(id)
      {
        this.addMarker({
          id: id,
          centre: this.centre,
          activity: this.activity,
          start_hour: this.start_hour,
          end_hour: this.end_hour,
          latitude: this.place.location.lat,
          longitude: this.place.location.lng,
          position: {
            lat: this.place.location.lat,
            lng: this.place.location.lng
          },
          address: this.place.formatted_address,
          place_id: this.place.place_id,
          clickable: true,
          infoText: this.getTemplate(
            this.centre,
            this.activity,
            this.start_hour,
            this.end_hour,
            this.place.formatted_address
          ),
        })
        this.centerMap(this.place.location)
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
        this.$vuetify.goTo('#gac-datatable')
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