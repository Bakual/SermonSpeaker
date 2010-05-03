<?php
/**
* @version $Id:  $
* @package sermonSpeaker
* @Email steve@sermonSpeaker.us
* @Website - http://sermonSpeaker.us
* @copyright Copyright (C) 2006 Steve Shiflett. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* This file is part of sermonSpeaker.
* sermonSpeaker is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* sermonSpeaker is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* See COPYRIGHT.php for copyright notices and details.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class sermonLang {
	var $Notes="Notizen";
	var $bio="Biografie";
	var $delete="L&ouml;schen";
	var $download_hoover_tag="herunterladen";
	var $help="Hilfe";
	var $helpText="This is where the help text will go.";
	var $latest="Letzte";
	var $latest_sermons = "Letzte Predigten";
	var $singlespeaker ="Titel der Predigtserien";
	var $singleseries="Die Predigtserien";
	var $singlesermon="Herunterladen";
	var $speakermain = "Unsere Prediger";
	var $no_speakers="Zur Zeit sind keine Prediger verfügbar";	
	var $myPlaylist="Meine Play-Liste";
	var $newWindow="Öffnet den Player in einem neuen Fenster";
	var $next="N&auml;chste Seite";
	var $of="von";
	var $page="Seite";
	var $play="Abspielen";
	var $playEntireCd="ganze CD abspielen";
	var $playPlaylist="ganze Playlist abspielen";
	var $playlistAdd="Zur Playliste hinzufügen";
	var $playlistNotWritable="Playlist kann nicht gespeichert werden!";
	var $previous="Vorherige Seite";
	var $scripture="Referenz";
	var $search="Ergebnis der Suche";
	var $search_box_speakermain="Prediger";
	var $search_box_singlespeaker="Thema der Predigtserie";
	var $search_box_singleseries="Predigt";
	var $search_box_singlesermon="Predigt herunterladen";
	var $series="Serie";
	var $series_hoover_tag="Themenauswahl der Predigten";
	var $seriesDescription="Beschreibung";
	var $series_select_hoover_tag="Predigten dieser Serie anzeigen.";
	var $seriesTitle="Titel";
	var $sermon="Predigt";
	var $sermon_hoover_tag = "Einzelne Predigten";
	var $sermon_date="Datum";
	var $sermonName="Titel";
	var $sermonNotes="Notizen";
	var $sermon_series="Serien";
	var $sermonTime="Dauer";
	var $sermons="Predigten";
	var $sermons_of="Predigten von";
	var $single_sermon_hoover_tag="Notizen anzeigen und herunterladen";
	var $speaker="Prediger";
	var $speakers="Prediger";
	var $toSave="Zum Speichern: Hier rechtsklicken und \"Speichern unter...\" w&auml;hlen";
	var $totalResults="Total Resultate: ";
	var $sermonNumber="Predigt Number";
	var $viewSeriess="Serien anzeigen";
	var $viewSermons="Predigten anzeigen";
	var $viewSpeakers="Prediger anzeigen";
	var $web_link_description="Seine Webseite";
	var $web_link_tag="Webseite von ";
  var $no_sermon="Zur Zeit sind keine Predigten verf&uuml;gbar.";	
  var $seriesmain="Serien";
  var $sermonlist="Alle Predigten";
  var $seriessermons="Predigten nach Serieen geordned";
  var $ls_title="Neueste Predigten";
  var $playtoplay="Abspielen";
  var $sortdate="Predigten sortiert nach Datum";
  var $sortpub="Predigten sortiert nach Ver&ouml;ffentlichung";
  var $sortview="H&auml;ufigst angesehene Predigten";
  var $sortalph="Predigten alphabetisch geordnet";
  var $serdate="Predigt Datum";
  var $serpub="Neueste";
  var $serview="Beliebtheit";
  var $seralph="alphabetisch";
  var $sersortby="Predigten sortiert nach: ";
  var $first="Erste Seite";
  var $last="Letzte Seite";
  var $nxt="Weiter";
  var $prev="Zur&uuml;ck";
  var $addfile="Weitere Downloads";
  var $mnt = array("01" => "Januar", "02" => "Februar", "03" => "M&auml;rz", "04" => "April", "05" => "Mai", "06" => "Juni", "07" => "Juli", "08" => "August", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Dezember");
  var $sermfrom = "Predigten von";
  var $fullfeed="RSS feed";
  var $popup_player="Abspielen im neuen Fenster";
  var $sc_help="Hilfe zu Podcasting";
  var $sc_helpeditor=''; // Leer lassen falls nur eine Sprache verwendet wird, anderenfalls den code aus dem File sermoncastconfig.sermonspeaker.php hier einfgen!
  var $fu_ext="Dateityp wird nicht untersttzt";
  var $fu_logout="Abmelden";
  var $fu_another="Eine weitere Predigt hochladen";
  var $fu_cont="Bitte hier klicken";
  var $fu_failed="Etwas ist schiefgelaufen...";
  var $fu_exists="Datei existiert bereits!";
  var $fu_login="Bitte anmelden";
  var $fu_pwd="Passwort";
  var $fu_log="Anmelden";
  var $fu_reset="Zur&uuml;cksetzen";
  var $fu_welcome="Willkommen";
  var $fu_newsermon="Neue Predigt erstellen";
  var $fu_save="Speichern";
  var $fu_uploadok="erfolgreich gespeichert";
  var $fu_step="Schritt";
  var $fu_step1="Datei zum hochladen ausw&auml;hlen";
  var $fu_step2="Untenstehende Felder ausf&uuml;llen";
  var $fu_step3="Fertig";
  var $sermonTitle="Titel der Predigt";
  var $notes="Bemerkungen";
  var $published="Freigegeben";
  var $sermoncast="Podcast";
  var $fu_date_desc="Format: yyyy-mm-dd";
  var $fu_sermonTime_desc="Dauer in (hh:)mm.ss";
  var $fu_published_desc="Erscheint auf Website";
  var $fu_sermoncast_desc="Erscheint in Podcast/iTunes";
  var $filename="Datei";
  var $fu_upsavedok="Predigt erfolgreich gespeichert";
} ?>
