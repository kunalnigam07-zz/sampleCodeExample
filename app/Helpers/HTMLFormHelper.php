<?php

namespace App\Helpers;

class HTMLFormHelper
{
	public static function attributes($attributes)
	{
		$html = [];

		foreach ($attributes as $k => $v) {
			$html[] = ' ' . $k . '="' . $v . '"';
		}

		return implode(' ', $html);
	}

	public static function text($label, $name, $value, $errors, $attributes = [], $notes = '', $modifier = ['type' => '', 'data' => ''])
	{
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name . '-id';
		}

		$class = '';
		$tip = '';
		if ($errors->has($name)) {
			$class = 'error-alert';
			$tip = $errors->first($name);
		} elseif (strlen($notes) > 0) {
			$class = 'info-alert';
			$tip = $notes;
		}

		$text_field_pre = '';
		$text_field_post = '';
		switch ($modifier['type']) {
			case 'pre':
				$text_field_pre = '<table><tr><td>' . $modifier['data'] . '</td><td>';
				$text_field_post = '</td></tr></table>';

				$class .= ' prefix';
				break;
			case 'date':
				$text_field_pre = '<div class="datepicker">';
				$text_field_post = '<span class="cal-icon"><i class="fa fa-calendar-o"></i></span></div>';
				break;
		}

		$html = '<fieldset' . (strlen($class) > 0 ? ' class="' . $class . '"' : '') . '>
			<label for="' . $attributes['id'] . '">' . $label . '</label>
			<div class="field">
			' . $text_field_pre . '
			<input type="text" name="' . $name . '" value="' . e(old($name, $value)) . '"' . self::attributes($attributes) . '>
			' . $text_field_post . '
			' . (strlen($tip) > 0 ? '<span class="' . $class . '">' . $tip . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function textarea($label, $name, $value, $errors, $attributes = [], $notes = '')
	{
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name . '-id';
		}

		$class = '';
		$tip = '';
		if ($errors->has($name)) {
			$class = 'error-alert';
			$tip = $errors->first($name);
		} elseif (strlen($notes) > 0) {
			$class = 'info-alert';
			$tip = $notes;
		}

		$html = '<fieldset' . (strlen($class) > 0 ? ' class="' . $class . '"' : '') . '>
			<label for="' . $attributes['id'] . '">' . $label . '</label>
			<div class="field">
			<textarea name="' . $name . '"' . self::attributes($attributes) . '>' . e(old($name, $value)) . '</textarea>
			' . (strlen($tip) > 0 ? '<span class="' . $class . '">' . $tip . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function password($label, $name, $errors, $attributes = [], $notes = '')
	{
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name . '-id';
		}

		$class = '';
		$tip = '';
		if ($errors->has($name)) {
			$class = 'error-alert';
			$tip = $errors->first($name);
		} elseif (strlen($notes) > 0) {
			$class = 'info-alert';
			$tip = $notes;
		}

		$html = '<fieldset' . (strlen($class) > 0 ? ' class="' . $class . '"' : '') . '>
			<label for="' . $attributes['id'] . '">' . $label . '</label>
			<div class="field">
			<input type="password" name="' . $name . '" value=""' . self::attributes($attributes) . '>
			' . (strlen($tip) > 0 ? '<span class="' . $class . '">' . $tip . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function checkbox($label, $name, $data, $value, $errors, $attributes = [])
	{
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name . '-id';
		}

		$html = '<fieldset' . ($errors->has($name) ? ' class="error-alert"' : '') . '>
			<label for="' . $attributes['id'] . '">' . $label . '</label>
			<div class="field">
			<input name="' . $name . '" type="checkbox" data-label="' . $data . '"' . self::attributes($attributes) . (old($name, $value) ? ' checked' : '') . '>
			' . ($errors->has($name) ? '<span class="error-alert">' . $errors->first($name) . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function select($label, $name, $data, $value, $errors, $attributes = [], $notes = '')
	{
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name . '-id';
		}

		$class = '';
		$tip = '';
		if ($errors->has($name)) {
			$class = 'error-alert';
			$tip = $errors->first($name);
		} elseif (strlen($notes) > 0) {
			$class = 'info-alert';
			$tip = $notes;
		}

		$html = '<fieldset' . (strlen($class) > 0 ? ' class="' . $class . '"' : '') . '>
			<label for="' . $attributes['id'] . '">' . $label . '</label>
			<div class="field">
			<select name="' . $name . '" data-placeholder="Please Select..."' . self::attributes($attributes) . '>
			<option value="">Please Select...</option>';

		foreach ($data as $k => $v) {
			$html .= '<option value="' . $k . '"' . ($k == old($name, $value) ? ' selected' : '') . '>' . e($v) . '</option>';
		}

		$html .= '</select>
			' . (strlen($tip) > 0 ? '<span class="' . $class . '">' . $tip . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function radiogroup($label, $name, $data, $value, $errors, $attributes = [], $notes = '')
	{
        $class = '';
        $tip = '';
        if ($errors->has($name)) {
            $class = 'error-alert';
            $tip = $errors->first($name);
        } elseif (strlen($notes) > 0) {
            $class = 'info-alert';
            $tip = $notes;
        }

		$html = '<fieldset' . ($errors->has($name) ? ' class="error-alert"' : '') . '>
			<label>' . $label . '</label>
			<div class="field">';

		foreach ($data as $k => $v) {
			$html .= '<input type="radio" data-label="' . $v . '" value="' . $k . '" name="' . $name . '"' . ($k == old($name, $value) ? ' checked' : '') . self::attributes($attributes) . '>';
		}

		$html .= '' . (strlen($tip) > 0 ? '<span style="display:block;" class="' . $class . '">' . $tip . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function checkgroup($label, $name, $data, $value, $errors, $attributes = [], $overflow = 0, $multiple = false)
	{
		$html = '<fieldset' . ($errors->has($name) ? ' class="error-alert"' : '') . '>
			<label>' . $label . '</label>
			<div class="field"' . ($overflow > 0 ? ' style="overflow:auto;height:' . $overflow . 'px;"' : '') . '>';

		foreach ($data as $k => $v) {
            if ($multiple) {
                $html .= '<div class="col-md-3"><input type="checkbox" data-label="' . $v . '" value="' . $k . '" name="' . $name . '[]"' . (in_array($k, old($name, $value)) ? ' checked' : '') . self::attributes($attributes) . '></div>';
            } else {
                $html .= '<input type="checkbox" data-label="' . $v . '" value="' . $k . '" name="' . $name . '[]"' . (in_array($k, old($name, $value)) ? ' checked' : '') . self::attributes($attributes) . '><br>';
            }
			
		}

		$html .= '' . ($errors->has($name) ? '<span class="error-alert">' . $errors->first($name) . '</span>' : '') . '
			</div>
			</fieldset>';

		return $html;
	}

	public static function file($label, $name, $value, $errors, $attributes = [], $notes = '', $info = ['type' => '', 'preview' => ''])
	{
		if (!isset($attributes['id'])) {
			$attributes['id'] = $name . '-id';
		}

		$class = '';
		$tip = '';
		if ($errors->has($name)) {
			$class = 'error-alert';
			$tip = $errors->first($name);
		} elseif (strlen($notes) > 0) {
			$class = 'info-alert';
			$tip = $notes;
		}

		$html = '<fieldset' . (strlen($class) > 0 ? ' class="' . $class . '"' : '') . '><label for="' . $attributes['id'] . '">' . $label . '</label><div class="field clearfix">';

		if (strlen($value) > 0) {
	        if ($info['type'] == 'image') {
	        	$html .= '<div class="profile-preview"><img src="' . $info['preview'] . '" alt="img"></div>';
	        } elseif ($info['type'] == 'file') {
	        	$html .= '<div class="profile-preview"><a href="' . $info['preview'] . '" class="cancel" target="_blank">Click to view file</a></div>';
	        } elseif ($info['type'] == 'video') {
	        	$html .= '<div class="profile-preview"><video height="200" controls><source src="' . $info['preview'] . '" type="video/mp4"></video></div>';
	        }

	        $html .= '<input type="checkbox" name="delete-' . $name . '" data-label="Delete uploaded file?">';
    	}

		$html .= '<div class="file-actions"><input type="file" name="' . $name . '"' . self::attributes($attributes) . ' class="filenamezone"><p class="file-name"></p>' . (strlen($tip) > 0 ? '<span class="' . $class . '">' . $tip . '</span>' : '') . '</div></div></fieldset>';

		return $html;
	}

	public static function hidden($name, $value, $attributes = [])
	{
		$html = '<input type="hidden" name="' . $name . '" value="' . $value . '"' . self::attributes($attributes) . '>';

		return $html;
	}

	public static function submit($value, $attributes = [])
	{
		$html = '<input type="submit" value="' . $value . '"' . self::attributes($attributes) . '>';

		return $html;
	}

	public static function string($label, $value, $pre = '', $post = '', $notes = '')
	{
		$html = '<fieldset class="info-alert"><label>' . $label . '</label><div class="field"><p>' . $pre . e($value) . $post . '</p>' . (strlen($notes) > 0 ? '<span class="info-alert">' . $notes . '</span>' : '') . '</div></fieldset>';

		return $html;
	}

	public static function legend($value)
	{
		$html = '<fieldset><legend>' . $value . '</legend></fieldset>';

		return $html;
	}
}
