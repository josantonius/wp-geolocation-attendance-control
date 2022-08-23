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
  Vue.component('gmap-autocomplete', VueGoogleMaps.Autocomplete)
  Vue.component('gmap-map', VueGoogleMaps.Map)
  Vue.component('VueGoogleMapsPlacesAggregator', VueGoogleMapsPlacesAggregator.VueGoogleMapsPlacesAggregator)
  Vue.component('VueGoogleAutocomplete', VueGoogleAutocomplete.VueGoogleAutocomplete)

  Vue.use(VueGoogleMaps, {
    load: {
      key: geolocationAttendanceControlPlaces.google_api_key,
      libraries: 'places'
    },
  })
  new Vue({
    el: '#app',
    data: vm => ({
      selected: [],
      search: '',
      place: null,
      snackbar: false,
      snackbarMessage: 'Text',
      isEdit: false,
      state: true,
      persistent: false,
      centre: '',
      addressType: 'address',
      pageName: 'Centros',
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
      dialogSubtitleA: '',
      dialogSubtitleB: '',
      headers: [
        {
          text: 'ID',
          align: 'left',
          value: 'id',
          sortable: true
        },
        {
          text: 'Centro',
          align: 'left',
          value: 'centre',
          sortable: true
        },
        { text: 'Dirección', value: 'address', sortable: true, align: 'left' },
        { text: 'Estado', value: 'state', sortable: true, align: 'left' },
        { text: 'Acciones', value: 'centre', sortable: false, align: 'center' }
      ]
    }),

    created()
    {
      this.selectCentres()
      setTimeout(() =>
      {
        this.selectCentres()
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
        data.append('nonce', geolocationAttendanceControlPlaces.nonce)
        for (var key in params)
        {
          data.append(key, params[key])
          console.log(key, params[key])
        }
        return new Promise((resolve, reject) =>
        {
          return axios({
            method: type,
            url: geolocationAttendanceControlPlaces.ajax_url,
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
        this.markers = []
        this.request('post', 'select_centres').then((response) =>
        {
          console.log(response)
          response.forEach((marker) =>
          {
            marker.position = {
              lat: parseFloat(marker.latitude),
              lng: parseFloat(marker.longitude)
            }
            marker.infoText = this.getTemplate(
              marker.centre,
              marker.position.lat,
              marker.position.lng,
              marker.address
            )
            marker.value = false
            marker.clickable = true

            this.addMarker(marker)
          })
          console.log(this.markers)
          document.querySelector('.opacity-on').classList.add('opacity-off')
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      insertCentre()
      {
        var data = {
          centre: this.centre,
          latitude: this.place.location.lat,
          longitude: this.place.location.lng,
          address: this.place.formatted_address,
          place_id: this.place.place_id,
          state: this.state ? 1 : 0
        }
        this.request('post', 'insert_centre', data).then((response) =>
        {
          if (response)
          {
            this.saveMarker(response)
          }
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      dialogEditCentre(item)
      {
        console.log('ssss', item)
        this.place = {
          location: item.position,
          formatted_address: item.address,
          place_id: item.place_id
        }
        this.$refs.autocomplete.$refs.gmap.$refs.input.value = item.address
        this.customLatitude = item.position.lat
        this.customLongitude = item.position.lng
        this.lastCoordinates = this.customLatitude + ',' + this.customLongitude
        this.state = parseInt(item.state) === 1
        this.currentID = item.id
        this.centre = item.centre
        this.persistent = true
        this.isEdit = true
        this.dialog = true
      },

      updateCentre()
      {
        var data = {
          id: this.currentID,
          centre: this.centre,
          latitude: this.place.location.lat,
          longitude: this.place.location.lng,
          address: this.place.formatted_address,
          place_id: this.place.place_id,
          state: this.state ? 1 : 0
        }
        this.request('post', 'update_centre', data).then((response) =>
        {
          if (this.$refs.google_maps_places_aggregator.infoWinOpen)
          {
            this.$refs.google_maps_places_aggregator.infoWinOpen = false
          }
          this.editMarker()
        }).catch((error) =>
        {
          console.log(error)
        })
      },

      deleteCentre(currentID)
      {
        var data = {
          id: currentID
        }
        let index = this.markers.findIndex(item => item.id === currentID)
        this.request('post', 'delete_centre', data).then((response) =>
        {
          if (response)
          {
            this.markers.splice(index, 1)
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
        this.dialogTitle = '¿Eliminar centro?'
        this.dialogSubtitleA = 'Si eliminas "' + item.centre + '" se borrarán todas las actividades realizadas en este centro.'
        this.dialogSubtitleB = 'Ten en cuenta que al eliminar los centros y sus actividades también se eliminarán todos los registros de asistencia asociados con estas actividades.'
        this.dialogConfirm = true
        this.currentID = item.id
      },

      deleteItems()
      {
        this.dialogTitle = ''
        this.dialogSubtitleA = ''
        this.dialogSubtitleB = ''
        this.dialogConfirm = false
        if (this.currentID !== null)
        {
          this.deleteCentre(this.currentID)
        } else
        {
          this.deleteSelectedItems()
        }
      },

      deleteItemsAsk()
      {
        this.dialogTitle = '¿Eliminar centros?'
        this.dialogSubtitleA = 'Si los eliminas se borrarán todas las actividades realizadas en estos centros.'
        this.dialogSubtitleB = 'Ten en cuenta que al eliminar los centros y sus actividades también se eliminarán todos los registros de asistencia asociados con estas actividades.'
        this.dialogConfirm = true
      },

      deleteSelectedItems()
      {
        this.selected.forEach((item) =>
        {
          this.deleteCentre(item.id)
        })
        this.selected = []
      },

      openDialog()
      {
        this.$vuetify.goTo(0)
        document.body.scrollTo(0, 0)
        this.dialog = true
      },

      close()
      {
        this.dialog = false
        this.resetForm()
      },

      saveMarker(id, index)
      {
        this.addMarker({
          id: id,
          centre: this.centre,
          activity: this.activity,
          state: this.state ? 1 : 0,
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
            this.place.location.lat,
            this.place.location.lng,
            this.place.formatted_address
          ),
        }, index)
        this.centerMap(this.place.location)
        this.close()
        this.zoomMap(15)

      },

      editMarker()
      {
        let index = this.markers.findIndex(e => e.id == this.currentID)
        if (index !== -1)
        {
          this.markers.splice(index, 1)
          this.saveMarker(this.currentID)
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
        this.clear()
        this.centre = ''
        this.customLatitude = ''
        this.customLongitude = ''
        this.currentID = null
        this.place = null
        this.persistent = false
        this.isEdit = false
        this.state = true
        this.addressType = 'address'
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
        return this.centre !== '' && this.place
      },
      formTitle()
      {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      }
    }
  })
})