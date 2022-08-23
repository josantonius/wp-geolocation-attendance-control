<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eliasis\Framework\View;

$data = View::getOption();
?>

<v-content>
   <div>
	  <v-dialog
		 ref="start_date_selector"
		 v-model="startDateSelector"
		 :return-value.sync="startDate"
		 persistent
		 lazy
		 full-width
		 width="290px"
		 >
		 <v-date-picker locale="es-es" :first-day-of-week="1" color="indigo" v-model="startDate" scrollable>
			<v-spacer></v-spacer>
			<v-btn flat color="indigo" @click="startDateSelector = false">Cancelar</v-btn>
			<v-btn flat color="indigo" @click="saveDate('start')">OK</v-btn>
		 </v-date-picker>
	  </v-dialog>
	  <v-dialog
		 ref="end_date_selector"
		 v-model="endDateSelector"
		 :return-value.sync="endDate"
		 persistent
		 lazy
		 full-width
		 width="290px"
		 >
		 <v-date-picker locale="es-es" :first-day-of-week="1" color="indigo" v-model="endDate" scrollable>
			<v-spacer></v-spacer>
			<v-btn flat color="indigo" @click="endDateSelector = false">Cancelar</v-btn>
			<v-btn flat color="indigo" @click="saveDate('end')">OK</v-btn>
		 </v-date-picker>
	  </v-dialog>
	  <v-layout align-center justify-center>
		 <v-card
			class="elevation-3"
			width="100%" max-width="1200">
			<vue-google-maps-places-aggregator
			ref="google_maps_places_aggregator"
			:markers="markers"
			:zoom="zoom"
			:center="center"
			:width="width"
			:height="height"
			:max-width="maxWidth"
			:max-height="maxHeight"
			@on-open-marker="onOpenMarker"
			@on-close-marker="onCloseMarker"
			@on-click-marker="onCLickMarker"
			></vue-google-maps-places-aggregator>
			<br>
			<v-card-title>
			   <br>
			   <v-btn color="indigo" v-if="!selected.length && markers.length" dark class="mb-2" @click="createCSV(markers, true)">Descargar registros</v-btn>
			   <v-btn color="indigo" v-if="selected.length" dark class="mb-2" @click="createCSV(selected, false)">Descargar seleccionados</v-btn>
			   <v-btn color="red" dark class="mb-2" v-if="selected.length" @click="deleteItemsAsk">Eliminar seleccionados</v-btn>
			   <v-spacer></v-spacer>
			   <v-text-field
				  v-if="markers.length"
				  v-model="search"
				  append-icon="search"
				  label="Buscar"
				  single-line
				  hide-details
				  ></v-text-field>
			</v-card-title>
			<v-data-table
			   id="gac-datatable"
			   v-model="selected"
			   :headers="headers"
			   :items="markers"
			   item-key="id"
			   :pagination.sync="pagination"
			   no-data-text="No se encontraron registros en el intervalo de fechas seleccionado"
			   no-results-text="No se encontraron coincidencias"
			   rows-per-page-text="Filas por página"
			   :search="search"
			   select-all
			   >
			   <template slot="items" slot-scope="props">
				  <td>
					 <v-checkbox
						v-model="props.selected"
						primary
						hide-details
						></v-checkbox>
				  </td>
				  <td>{{ props.item.id }}</td>
				  <td class="text-xs-center">
					 <v-chip
						 class="status-chip"
						 :color="parseInt(props.item.action_status) ? 'green' : 'red'"
						 :title="'Se encontraba a ' + props.item.meters_apart + ' metros de la ubicación del centro'"
						 label>{{ props.item.action }}</v-chip>
				  </td>
				  <td class="text-xs-center">
					 <v-chip
					 class="status-chip"
					 :color="parseInt(props.item.hour_status) ? 'green' : 'red'"
					 :title="props.item.fullhour" label>{{ props.item.hour }}</v-chip>
				  </td>
				  <td class="text-xs-right">{{ props.item.user_fullname }}</td>
				  <td class="text-xs-right">{{ props.item.centre_name }}</td>
				  <td class="text-xs-right">{{ props.item.activity_name }}</td>
				  <td class="text-xs-right">{{ props.item.date }}</td>
				  <td class="justify-center layout px-0">
					 <v-icon
						title="Descargar registro"
						color="#009688"
						class="mr-2"
						@click="createCSV([props.item], false)"
						>
						cloud_download
					 </v-icon>
					 <v-icon
						title="Ver en mapa"
						color="indigo"
						class="mr-2"
						@click="goToMarker(props.item)"
						>
						location_on
					 </v-icon>
					 <v-icon
						title="Eliminar centro"
						color="red"
						@click="deleteItemAsk(props.item)"
						>
						delete
					 </v-icon>
				  </td>
			   </template>
			   <template slot="no-data">
			   </template>
			</v-data-table>
		 </v-card>
	  </v-layout>
	  <v-dialog
		 v-model="dialogConfirm"
		 max-width="290"
		 >
		 <v-card>
			<v-card-title class="headline">{{ dialogTitle }}</v-card-title>
			<v-card-text>
			   {{ dialogSubtitle }}
			</v-card-text>
			<v-card-actions>
			   <v-spacer></v-spacer>
			   <v-btn
				  color="indigo"
				  flat="flat"
				  @click="dialogConfirm = false"
				  >
				  Cancelar
			   </v-btn>
			   <v-btn
				  color="indigo"
				  flat="flat"
				  @click="deleteItems()"
				  >
				  Aceptar
			   </v-btn>
			</v-card-actions>
		 </v-card>
	  </v-dialog>
   </div>
</v-content>
