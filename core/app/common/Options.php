<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

class Options {
	public static function ajaxGetAutoConvtStatus() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::getAutoConvtStatus());
		wp_die();
	}
	public static function getAutoConvtStatus() {
		return (bool)get_option('avifautoconvstatus', false);
	}

	public static function ajaxSetAutoConvtStatus() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::setAutoConvtStatus());
		wp_die();
	}
	public static function setAutoConvtStatus() {
		$autoConvStatus = self::getAutoConvtStatus();
		return update_option('avifautoconvstatus', !$autoConvStatus);
	}

	public static function ajaxGetOperationMode() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::getOperationMode());
		wp_die();
	}

	public static function getOperationMode() {
		$opMode = get_option('avifoperationmode', false);
		if ($opMode == false) {
			update_option('avifoperationmode', sanitize_text_field('inactive'));
		}
		return get_option('avifoperationmode');
	}


	public static function ajaxSetOperationMode() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::setOperationMode(sanitize_text_field($_POST['mode'])));
		wp_die();
	}

	public static function setOperationMode(string $value = '') {
		if ($value == '') return;
		return update_option('avifoperationmode', $value);
	}

	public static function ajaxGetImgQuality() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::getImageQuality());
		wp_die();
	}
	public static function getImageQuality() {
		return intval(get_option('avifimagequality', 70));
	}

	public static function ajaxSetImgQuality() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::setImgQuality(intval($_POST['quality'])));
		wp_die();
	}
	public static function setImgQuality($value = '') {
		if ($value == '') return;
		return update_option('avifimagequality', $value);
	}

	public static function ajaxGetComSpeed() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::getComSpeed());
		wp_die();
	}
	public static function getComSpeed() {
		return intval(get_option('avifcompressionspeed', 6));
	}
	public static function ajaxSetComSpeed() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::setComSpeed(intval($_POST['speed'])));
		wp_die();
	}
	public static function setComSpeed($value = '') {
		if ($value == '') return;
		return update_option('avifcompressionspeed', $value);
	}

	public static function ajaxGetConversionEngine(){
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::getConversionEngine());
		wp_die();
	}

	public static function getConversionEngine(){
		$engine = get_option('avifconversionengine',false);
		if($engine == false){
			update_option('avifconversionengine',sanitize_text_field('cloud'));
		}
		return get_option('avifconversionengine');
	}

	public static function ajaxSetConversionEngine(){
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::setConversionEngine(sanitize_text_field($_POST['engine'])));
		wp_die();
	}

	public static function setConversionEngine($value = ''){
		if($value == '') return;
		return update_option('avifconversionengine',$value);
	}

	public static function ajaxGetOnTheFlyAvif(){
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::getOnTheFlyAvif());
		wp_die();
	}
	public static function getOnTheFlyAvif(){
		return (bool)get_option('avifontheflyavif',false);
	}

	public static function ajaxSetOnTheFlyAvif(){
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::setOnTheFlyAvif());
		wp_die();
	}
	public static function setOnTheFlyAvif(){
		$onTheFlyAvif = self::getOnTheFlyAvif();
		return update_option('avifontheflyavif',!$onTheFlyAvif);
	}

}
