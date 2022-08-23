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
	  <v-card class="add-activity-form">
		 <v-card-title>
			<span class="headline">{{ isEdit ? 'Editar actividad' : 'Nueva actividad' }}</span>
			<div class="switch-state" :title="state ? 'Desactivar actividad' : 'Activar actividad'">
			   <v-switch
				  v-show="isEdit"
				  v-model="state"
				  color="teal"
				  hide-details
				  ></v-switch>
			</div>
		 </v-card-title>
		 <v-card-text class="add-activity-form-content">
			<v-container grid-list-md>
			   <v-layout wrap>
				  <v-flex xs12>
					 <v-combobox light
						v-model="selectedActivity"
						:items="activitiesList"
						label="Actividad"
						></v-combobox>
				  </v-flex>
				  <v-flex xs12>
					 <v-autocomplete
						:items="centres"
						v-model="selectedCentre"
						no-data-text="Centro no entontrado"
						label="Centro"
						item-text="centre"
						item-value="id"
						return-object
						></v-autocomplete>
				  </v-flex>
				  <v-flex xs12 sm6>
					 <v-dialog
						ref="start_hour"
						v-model="startTime"
						:return-value.sync="start_hour"
						persistent
						lazy
						full-width
						width="290px"
						>
						<v-text-field
						   slot="activator"
						   v-model="start_hour"
						   label="Hora de inicio"
						   readonly
						   ></v-text-field>
						<v-time-picker
						   color="indigo"
						   v-if="startTime"
						   v-model="start_hour"
						   full-width
						   format="24hr"
						   >
						   <v-spacer></v-spacer>
						   <v-btn flat color="indigo" @click="startTime = false">Cancelar</v-btn>
						   <v-btn flat color="indigo" @click="$refs.start_hour.save(start_hour)">OK</v-btn>
						</v-time-picker>
					 </v-dialog>
				  </v-flex>
				  <v-flex xs12 sm6>
					 <v-dialog
						ref="end_hour"
						v-model="endTime"
						:return-value.sync="end_hour"
						persistent
						lazy
						full-width
						width="290px"
						>
						<v-text-field
						   slot="activator"
						   v-model="end_hour"
						   label="Hora de finalización"
						   readonly
						   ></v-text-field>
						<v-time-picker
						   color="indigo"
						   v-if="endTime"
						   v-model="end_hour"
						   full-width
						   format="24hr"
						   >
						   <v-spacer></v-spacer>
						   <v-btn flat color="indigo" @click="endTime = false">Cancelar</v-btn>
						   <v-btn flat color="indigo" @click="$refs.end_hour.save(end_hour)">OK</v-btn>
						</v-time-picker>
					 </v-dialog>
				  </v-flex>
				  <v-flex xs12 sm6 class="text-xs-center">
					 <v-select
						:items="days"
						v-model="selectedDays"
						label="Días"
						item-text="text"
						item-value="value"
						multiple
						v-if="false"
						>
						<template
						   slot="selection"
						   slot-scope="{ item, index }"
						   >
						   <span
							  v-if="selectedDays.length === days.length && index === 0"
							  class="grey--text caption"
							  >
						   Todos los días
						   </span>
						   <span
							  v-if="selectedDays.length !== days.length && index === 0"
							  class="grey--text caption"
							  >
						   {{ item.text }}... (
						   </span>
						   <span
							  v-if="selectedDays.length !== days.length && index === 1"
							  class="grey--text caption"
							  >
						   + otros {{ selectedDays.length - 1 }})
						   </span>
						</template>
					 </v-select>
				  </v-flex>
				  <v-flex xs12 sm6 class="text-xs-center">
					 <v-select
						:items="months"
						v-model="selectedMonths"
						label="Meses"
						item-text="text"
						item-value="value"
						multiple
						v-if="false"
						>
						<template
						   slot="selection"
						   slot-scope="{ item, index }"
						   >
						   <span
							  v-if="selectedMonths.length === months.length && index === 0"
							  class="grey--text caption"
							  >
						   Todos los meses
						   </span>
						   <span
							  v-if="selectedMonths.length !== months.length && index === 0"
							  class="grey--text caption"
							  >
						   {{ item.text }}... (
						   </span>
						   <span
							  v-if="selectedMonths.length !== months.length && index === 1"
							  class="grey--text caption"
							  >
						   + otros {{ selectedMonths.length - 1 }})
						   </span>
						</template>
					 </v-select>
				  </v-flex>
			   </v-layout>
			</v-container>
		 </v-card-text>
		 <v-card-actions>
			<v-spacer></v-spacer>
			<v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
			<v-btn color="blue darken-1" :disabled="!valid" flat @click="isEdit ? updateActivity() : insertActivity()">
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
			<v-card-title>
			   <br>
			   <v-btn color="indigo" dark class="mb-2" v-if="!selected.length" @click="openDialog()">Nueva actividad</v-btn>
			   <v-btn color="red" dark class="mb-2" v-if="selected.length" @click="deleteItemsAsk">Eliminar seleccionadas</v-btn>
			   <v-spacer></v-spacer>
			   <v-text-field
				  v-if="activities.length"
				  v-model="search"
				  append-icon="search"
				  label="Buscar"
				  single-line
				  hide-details
				  ></v-text-field>
			</v-card-title>
			<v-data-table
			   v-model="selected"
			   ref="datatable"
			   :headers="headers"
			   :items="activities"
			   item-key="id"
			   class="elevation-1"
			   :pagination.sync="pagination"
			   no-data-text="No hay actividades disponibles"
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
				  <td class="text-xs-left">{{ props.item.activity }}</td>
				  <td class="text-xs-left">{{ props.item.centre }}</td>
				  <td class="text-xs-left">{{ props.item.start_hour }}</td>
				  <td class="text-xs-left">{{ props.item.end_hour }}</td>
				  <td class="text-xs-left">
					 <span class="parseInt(props.item.state) === 1 ? 'gac-active' : 'gac-inactive'">
					 {{ parseInt(props.item.state) === 1 ? 'Activa' : 'Inactiva' }}
					 </span>
				  </td>
				  <td class="justify-center layout px-0">
					 <v-icon
						title="Editar centro"
						color="teal"
						class="mr-2"
						@click="dialogEditActivity(props.item)"
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
