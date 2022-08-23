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
   <v-snackbar
	  v-model="snackbar"
	  :bottom="true"
	  :timeout="timeout"
	  >
	  {{ snackbarMessage }}
	  <v-btn
		 color="red"
		 flat
		 @click="snackbar = false"
		 >
		 Cerrar
	  </v-btn>
   </v-snackbar>
   <v-dialog v-model="dialog" :persistent="persistent" max-width="500px">
	  <v-card class="add-centre-form">
		 <v-card-title>
			<span class="headline">{{ isEdit ? 'Editar centro' : 'Nuevo centro' }}</span>
			<div class="switch-state" :title="state ? 'Desactivar centro' : 'Activar centro'">
			   <v-switch
				  v-show="isEdit"
				  v-model="state"
				  color="teal"
				  hide-details
				  ></v-switch>
			</div>
		 </v-card-title>
		 <v-card-text class="add-centre-form-content">
			<v-container grid-list-md>
			   <v-layout wrap>
				  <v-flex xs12
					 class="text-xs-center">
					 <v-btn-toggle v-model="addressType" mandatory>
						<v-btn flat value="address">
						   Dirección
						</v-btn>
						<v-btn flat value="coordinates">
						   Coordenadas
						</v-btn>
					 </v-btn-toggle>
				  </v-flex>
				  <v-flex xs12>
					 <v-text-field
						v-model="centre"
						label="Centro"
						></v-text-field>
				  </v-flex>
				  <v-flex xs12 v-show="addressType === 'address'">
					 <div class="v-input v-text-field theme--light">
						<div class="v-input__control">
						   <div class="v-input__slot">
							  <div class="v-text-field__slot">
								 <vue-google-autocomplete
								 ref="autocomplete"
								 :placeholder="placeholder"
								 class-name="className"
								 id="id"
								 :options="options"
								 @focus="onFocus"
								 @blur="onBlur"
								 @change="onChange"
								 @keypress="onKeyPress"
								 @place-changed="onPlaceChanged"
								 ></vue-google-autocomplete>
							  </div>
						   </div>
						</div>
					 </div>
				  </v-flex>
				  <v-flex v-show="addressType === 'coordinates'" xs12 sm6>
					 <v-text-field
						v-model="customLatitude"
						label="Latitud"
						@blur="onChangeCoordinates()"
						@keypress="onChangeCoordinates()"
						></v-text-field>
				  </v-flex>
				  <v-flex v-show="addressType === 'coordinates'" xs12 sm6>
					 <v-text-field
						v-model="customLongitude"
						label="Longitud"
						@blur="onChangeCoordinates()"
						@keypress="onChangeCoordinates()"
						></v-text-field>
				  </v-flex>
			   </v-layout>
			</v-container>
		 </v-card-text>
		 <v-card-actions>
			<v-spacer></v-spacer>
			<v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
			<v-btn color="blue darken-1" :disabled="!valid" flat @click="isEdit ? updateCentre() : insertCentre()">
			   {{ isEdit ? 'Actualizar' : 'Agregar' }}
			</v-btn>
		 </v-card-actions>
	  </v-card>
   </v-dialog>
   <div>
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
			<v-card-title>
			   <br>
			   <v-btn color="indigo" dark class="mb-2" v-if="!selected.length" @click="openDialog()">Nuevo centro</v-btn>
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
			   v-model="selected"
			   :headers="headers"
			   :items="markers"
			   item-key="id"
			   class="elevation-1"
			   :pagination.sync="pagination"
			   no-data-text="No hay centros disponibles"
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
				  <td class="text-xs-left">{{ props.item.centre }}</td>
				  <td class="text-xs-left">{{ props.item.address }}</td>
				  <td class="text-xs-left">
					 <span class="parseInt(props.item.state) === 1 ? 'gac-active' : 'gac-inactive'">
					 {{ parseInt(props.item.state) === 1 ? 'Activo' : 'Inactivo' }}
					 </span>
				  </td>
				  <td class="justify-center layout px-0">
					 <v-icon
						title="Ver en mapa"
						color="indigo"
						class="mr-2"
						@click="goToMarker(props.item)"
						>
						location_on
					 </v-icon>
					 <v-icon
						title="Editar centro"
						color="teal"
						class="mr-2"
						@click="dialogEditCentre(props.item)"
						>
						edit
					 </v-icon>
					 <v-icon
						title="Eliminar centro"
						color="red"
						class="mr-2"
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
			   {{ dialogSubtitleA }}
			   <br><br>
			   {{ dialogSubtitleB }}
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
