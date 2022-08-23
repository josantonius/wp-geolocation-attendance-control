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

  new Vue({
    el: '#app',
    data: vm => ({
      selectedUsers: [2],
      users: [
        {
          avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg',
          headline: 'Alex Díaz',
          title: 'Ali Connors',
          subtitle: "I'll be in your neighborhood doing errands this weekend. Do you want to hang out?"
        },
        {
          avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg',
          headline: 'Summer BBQ',
          title: 'me, Scrott, Jennifer',
          subtitle: "Wish I could come, but I'm out of town this weekend."
        },
        {
          avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg',
          headline: 'Oui oui',
          title: 'Sandra Adams',
          subtitle: 'Do you have Paris recommendations? Have you ever been?'
        },
        {
          avatar: 'https://cdn.vuetifyjs.com/images/lists/4.jpg',
          headline: 'Birthday gift',
          title: 'Trevor Hansen',
          subtitle: 'Have any ideas about what we should get Heidi for her birthday?'
        },
        {
          avatar: 'https://cdn.vuetifyjs.com/images/lists/5.jpg',
          headline: 'Recipe to try',
          title: 'Britta Holt',
          subtitle: 'We should eat this: Grate, Squash, Corn, and tomatillo Tacos.'
        }
      ],
      centres: [],
      selectedCentre: null,
      activities: [],
      selectedActivity: null,
      selected: [],
      search: '',
      place: null,
      days: [
        {
          text: 'Lunes',
          value: 1,
        },
        {
          text: 'Martes',
          value: 2,
        },
        {
          text: 'Miércoles',
          value: 3,
        },
        {
          text: 'Jueves',
          value: 4,
        },
        {
          text: 'Viernes',
          value: 5,
        },
        {
          text: 'Sábado',
          value: 6,
        },
        {
          text: 'Domingo',
          value: 7,
        }
      ],
      selectedDays: [1, 2, 3, 4, 5, 6, 7],
      months: [
        {
          text: 'Enero',
          value: 1,
        },
        {
          text: 'Febrero',
          value: 2,
        },
        {
          text: 'Marzo',
          value: 3,
        },
        {
          text: 'Abril',
          value: 4,
        },
        {
          text: 'Mayo',
          value: 5,
        },
        {
          text: 'Junio',
          value: 6,
        },
        {
          text: 'Julio',
          value: 7,
        },
        {
          text: 'Agosto',
          value: 8,
        },
        {
          text: 'Septiembre',
          value: 9,
        },
        {
          text: 'Octubre',
          value: 10,
        },
        {
          text: 'Noviembre',
          value: 11,
        },
        {
          text: 'Diciembre',
          value: 12,
        }
      ],
      selectedMonths: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
      snackbar: false,
      snackbarMessage: 'Text',
      isEdit: false,
      dialogUsers: true,
      state: true,
      persistent: false,
      centre: '',
      addressType: 'address',
      pageName: 'Actividades',
      customLatitude: '',
      customLongitude: '',
      lastCoordinates: null,
      activity: '',
      headerTitle: 'Centros',
      startTime: false,
      display: true,
      start_hour: null,
      endTime: false,
      patt: /^\s*[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)\s*,\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)\s*$/,
      end_hour: null,
      timeout: 15000,
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
      height: '400px',
      page: 'centres',
      maxHeight: '600px',
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
      headers: [
        {
          text: 'ID',
          align: 'left',
          value: 'id',
          sortable: true
        },
        {
          text: 'Actividad',
          align: 'left',
          value: 'activity',
          sortable: true
        },
        {
          text: 'Centro',
          align: 'left',
          value: 'centre',
          sortable: true
        },
        { text: 'Inicio', value: 'start_hour', sortable: true, align: 'left' },
        { text: 'Fin', value: 'end_hour', sortable: true, align: 'left' },
        { text: 'Estado', value: 'state', sortable: true, align: 'left' },
        { text: 'Acciones', value: 'centre', sortable: false, align: 'center' }
      ]
    }),

    created()
    {
      this.selectCentres()
      this.selectActivities()
      setTimeout(() =>
      {
        this.selectCentres()
        this.selectActivities()
      }, 180000)
    },

    methods: {
      getTemplate(centre, latitude, longitude, address)
      {
        return `
          <div class="gac-content">
            <div class="gac-heading">
              <h1 class="gac-first-heading">${centre}</h1>
              <h3 class="gac-second-heading">${address}</span></h3>
              
            </div>
            <div class="gac-bodyContent">
              <p>${latitude}, ${longitude}</p>
            </div>
          </div>
        `
      },

      onChangeCoordinates()
      {
        console.log('onChangeCoordinates')
        let coordinates = this.customLatitude + ',' + this.customLongitude
        coordinates = coordinates.replace(/ /g, '')
        if (!this.patt.test(coordinates) || (coordinates === this.lastCoordinates))
        {
          return false
        }
        this.setAddressByCoordinates(
          parseFloat(this.customLatitude),
          parseFloat(this.customLongitude)
        )
        this.lastCoordinates = coordinates
      },

      setAddressByCoordinates(latitude, longitude)
      {
        /* eslint-disable */
        let googleMaps = google.maps
        /* eslint-enable */
        let geocoder = new googleMaps.Geocoder()
        let latlng = new googleMaps.LatLng(latitude, longitude)
        geocoder.geocode({ 'location': latlng }, (results, status) =>
        {
          console.log('setAddressByCoordinates')
          if (results[0] && status === googleMaps.GeocoderStatus.OK)
          {
            this.$refs.autocomplete.setPlace(results[0])
            this.$refs.autocomplete.$refs.gmap.$refs.input.value = results[0].formatted_address
          } else
          {
            this.clear()
          }
        })
      },

      request(type, action, params)
      {
        var data = new URLSearchParams()
        params = params || {}
        data.append('action', action)
        data.append('nonce', geolocationAttendanceControlActivities.nonce)
        for (var key in params)
        {
          data.append(key, params[key])
          console.log(key, params[key])
        }
        return new Promise((resolve, reject) =>
        {
          return axios({
            method: type,
            url: geolocationAttendanceControlActivities.ajax_url,
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

      selectCentres()
      {
        this.request('post', 'select_all_centres').then((response) =>
        {
          this.centres = response
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      selectActivities()
      {
        this.markers = []
        this.request('post', 'select_activities').then((response) =>
        {
          this.activities = response
          console.log(response)
          document.querySelector('.opacity-on').classList.add('opacity-off')
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      getData()
      {
        let centreID = this.selectedCentre.id
        let index = this.centres.findIndex(item => item.id === centreID)
        return {
          id: this.currentID,
          centre_id: centreID,
          centre: this.centres[index].centre,
          activity: this.selectedActivity,
          start_hour: this.start_hour,
          end_hour: this.end_hour,
          // days: (this.selectedDays || '').toString(),
          // months: (this.selectedMonths || '').toString(),
          days: '',
          months: '',
          state: this.state ? 1 : 0
        }
      },

      insertActivity()
      {
        var data = this.getData()
        console.log(data)
        this.request('post', 'insert_activity', data).then((response) =>
        {
          console.log('RES', response)
          if (response)
          {
            data.id = response
            this.activities.push(data)
            this.close()
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      dialogEditActivity(item)
      {
        console.log('ssss', item)
        let index = this.centres.findIndex(i => i.id === item.centre_id)
        this.state = parseInt(item.state) === 1
        this.currentID = item.id
        this.selectedCentre = this.centres[index]
        this.selectedActivity = item.activity
        this.start_hour = item.start_hour
        this.end_hour = item.end_hour
        let selectedDays = item.days.split(',') || []
        this.selectedDays = selectedDays.map((x) => { return parseInt(x) })
        let selectedMonths = item.months.split(',') || []
        this.selectedMonths = selectedMonths.map((x) => { return parseInt(x) })
        this.state = parseInt(item.state) === 1
        this.persistent = true
        this.isEdit = true
        this.dialog = true
      },

      updateActivity()
      {
        let data = this.getData()
        data.id = this.currentID
        this.request('post', 'update_activity', data).then((response) =>
        {
          this.editActivity()
          this.close()
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      deleteActivity(currentID)
      {
        var data = {
          id: currentID
        }
        let index = this.activities.findIndex(item => item.id === currentID)
        this.request('post', 'delete_activity', data).then((response) =>
        {
          if (response)
          {
            this.activities.splice(index, 1)
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
        if (place.location.lat && place.location.lng && place.place_id && place.formatted_address)
        {
          this.place = place
        } else
        {
          this.place = null
        }
        this.customLatitude = place.location.lat
        this.customLongitude = place.location.lng
        console.info('onPlaceChanged', place)
      },

      clear()
      {
        this.place = null
        this.$refs.autocomplete.clear()
        this.$refs.autocomplete.$refs.gmap.$refs.input.value = ''
      },

      deleteItemAsk(item)
      {
        this.dialogTitle = '¿Eliminar actividad?'
        this.dialogSubtitle = 'Si eliminas "' + item.activity + '" se borrarán todos los registros de asistencias asociados con esta actividad.'
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
          this.deleteActivity(this.currentID)
        } else
        {
          this.deleteSelectedItems()
        }
      },

      deleteItemsAsk()
      {
        this.dialogTitle = '¿Eliminar actividades?'
        this.dialogSubtitle = 'Si las eliminas se borrarán todos los registros de asistencias asociados con estas actividades.'
        this.dialogConfirm = true
      },

      deleteSelectedItems()
      {
        this.selected.forEach((item) =>
        {
          this.deleteActivity(item.id)
        })
        this.selected = []
      },

      openDialog()
      {
        this.$vuetify.goTo(0)
        document.body.scrollTo(0, 0)
        this.dialog = true
      },

      toggleUser(index)
      {
        const i = this.selectedUsers.indexOf(index)
        if (i > -1)
        {
          this.selectedUsers.splice(i, 1)
        } else
        {
          this.selectedUsers.push(index)
        }
      },

      close()
      {
        this.dialog = false
        this.isEdit = false
        this.resetForm()
      },

      editActivity()
      {
        let index = this.activities.findIndex(e => parseInt(e.id) === parseInt(this.currentID))
        if (index !== -1)
        {
          let data = this.getData()
          this.activities[index].activity = data.activity
          this.activities[index].centre = this.selectedCentre.centre
          this.activities[index].centre_id = this.selectedCentre.id
          this.activities[index].days = data.days
          this.activities[index].end_hour = data.end_hour
          this.activities[index].id = data.id
          this.activities[index].months = data.months
          this.activities[index].start_hour = data.start_hour
          this.activities[index].state = data.state
          this.close()
        }
      },

      goToMarker(marker)
      {
        this.$refs.google_maps_places_aggregator.infoWinOpen = false
        this.$refs.google_maps_places_aggregator.infoWindowPos = marker.position
        this.$refs.google_maps_places_aggregator.infoOptions.content = marker.infoText
        this.$refs.google_maps_places_aggregator.infoWinOpen = true
        this.$vuetify.goTo(0)
        document.body.scrollTo(0, 0)
        this.centerMap(marker.position)
        this.zoomMap(15)
      },

      addMarker(marker, index)
      {
        if (index)
        {
          this.markers[index] = marker
        } else
        {
          this.markers.push(marker)
        }
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
        this.selectedCentre = null
        this.selectedActivity = null
        this.start_hour = null
        this.end_hour = null
        this.selectedDays = this.days
        this.selectedMonths = this.months
        this.state = true
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
      valid: function ()
      {
        console.log(this.selectedCentre, this.selectedActivity, this.start_hour, this.end_hour)
        return this.selectedCentre && this.selectedActivity && this.start_hour && this.end_hour
      },
      formTitle()
      {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },
      activitiesList()
      {
        let activities = []
        this.activities.forEach((item, index) =>
          activities.push(item.activity)
        )
        return activities
      }
    }
  })
})