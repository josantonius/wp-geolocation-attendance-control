<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eliasis\Framework\App;

$slug = App::GAC()->getOption( 'slug' );
?>

<div id="app" class="opacity-on">
<v-app>
<v-content>
<v-container fluid>
<v-layout align-center justify-center>
   <v-card width="100%" max-width="1200" class="gac-header-content" color="indigo">
	  <h2 v-if="page === 'attendances'" class="gac-header-title">
		 Registros desde 
		 <v-btn class="date-selector-btn" flat color="yellow" title="Pulsa para cambiar fecha" @click="startDateSelector = true"> {{ formattedStartDate }}</v-btn>
		 hasta 
		 <v-btn class="date-selector-btn" flat color="yellow" title="Pulsa para cambiar fecha" @click="endDateSelector = true">{{ formattedEndDate }}</v-btn>
	  </h2>
	  <h2 v-else class="gac-header-title">{{ pageName }}
	  </h2>
   </v-card>
</v-layout>





