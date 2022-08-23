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

<div id="app" class="opacity-on">
   <v-app>
	  <v-content>
		 <v-container fluid>
			<div>
			   <v-layout align-center justify-center>
				  <v-card
					 class="elevation-3"
					 width="100%" :max-width="maxWidth">
					 <img v-if="!successMessage && !errorMessage && !position" :src="getLoaderImage()" alt="">
					 <img v-if="false" :src="getErrorImage()" alt="">
					 <div class="geolocation-form" v-show="position && !positionReceived">
						<v-card-text>
						   <v-container grid-list-md>
							  <v-layout >
								 <v-flex xs12>
									<p  :style="{marginBottom: '22px', color: '#757575', fontWeight: '400', fontSize: '1.2rem'}" class="headlines text-xs-center">SELECCIONA TU ACTIVIDAD</p>
									<v-slide-y-transition>
									   <v-autocomplete
										  :items="centres"
										  v-model="selectedCentre"
										  no-data-text="Centro no encontrado"
										  label="Centro"
										  item-text="centre"
										  item-value="id"
										  return-object
										  v-show="centres.length > 0"
										  @change="onChangeCentre()"
										  ></v-autocomplete>
									</v-slide-y-transition>
									<v-slide-y-transition>
									   <v-autocomplete
										  :items="activitiesList"
										  v-model="selectedActivity"
										  no-data-text="Actividad no encontrada"
										  label="Actividad"
										  item-text="activity"
										  item-value="id"
										  return-object
										  v-show="activitiesList.length > 0"
										  @change="onChangeActivity()"
										  ></v-autocomplete>
									</v-slide-y-transition>
									<v-slide-y-transition>
									   <v-autocomplete
										  :items="schedulesList"
										  v-model="selectedSchedule"
										  no-data-text="Horario no encontrado"
										  label="Horario"
										  item-text="schedule"
										  item-value="id"
										  return-object
										  v-show="schedulesList.length > 0"
										  ></v-autocomplete>
									</v-slide-y-transition>
								 </v-flex>
							  </v-layout>
						   </v-container>
						</v-card-text>
						<v-slide-x-transition>
						   <v-card-actions :style="{marginTop: '-18px'}" v-if="position && !positionReceived && selectedSchedule">
							  <v-layout >
								 <v-flex xs12
									class="text-xs-center">
									<v-btn color="teal" dark class="mb-2" @click="setAttendance('Entrada')" >Llego ahora</v-btn>
									<v-btn color="pink" dark class="mb-2" @click="setAttendance('Salida')" >Salgo ahora</v-btn>
								 </v-flex>
							  </v-layout>
						   </v-card-actions>
						</v-slide-x-transition>
					 </div>
					 <v-fade-transition>
						<vue-google-maps-places-aggregator
						v-show="positionReceived && (successMessage || errorMessage)"
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
					 </v-fade-transition>
					 <v-card-title
						v-if="(!errorMessage && !position) || errorMessage || successMessage"
						style="width: 100%; display: inherit; text-align: center;"
						:style="successMessage ? {
						background: 'rgb(76, 175, 80)',
						margin: '6px auto 0px',
						position: 'relative',
						marginTop: '-1px',
						minHeight: '65px'
						} : (!errorMessage && !position) ? {
						background: '#eee',
						marginTop: '-5px',
						minHeight: '65px'
						} : {
						background: '#F44336',
						minHeight: '86px',
						position: 'relative',
						marginTop: '-1px'
						}">
						<p
						   v-if="successMessage"
						   :value="true"
						   style="margin: 0; color: white; font-size: 20px !important; font-weight: 400; letter-spacing: normal!important; font-family: Roboto,sans-serif!important;"
						   class="headlines"
						   >
						   {{ successMessage }}
						</p>
						<p
						   v-if="!errorMessage && !position"
						   :value="true"
						   style="margin: 0; color: #2196F3; font-size: 20px !important; font-weight: 400; letter-spacing: normal!important; font-family: Roboto,sans-serif!important;"
						   class="headlines"
						   >
						   SOLICITANDO UBICACIÓN...
						</p>
						<p
						   v-if="errorMessage"
						   style="margin: 0; color: white; font-size: 16px !important; font-weight: 400; letter-spacing: normal!important; font-family: Roboto,sans-serif!important;"
						   :value="true"
						   color="error"
						   class="headlines"
						   >
						   {{ errorMessage }}
						</p>
					 </v-card-title>
				  </v-card>
			   </v-layout>
			</div>
		 </v-container>
	  </v-content>
   </v-app>
</div>
