<?php

namespace App\Services\Admin;

use DB;
use App\Models\User;
use App\Models\Country;
use App\Models\ClassType;

abstract class AdminService
{
	protected $model;

	public function getEntry($id)
	{
		return $this->model->findOrFail($id);
	}

	public function newEntry($defaults)
	{
		foreach ($defaults as $k => $v) {
			$this->model->$k = $v;
		}

		return $this->model;
	}

    public function editEntry($data, $id)
	{
		return $this->model->where('id', $id)->update($data->except('_token', 'page_start_pp'));
	}

	public function createEntry($data)
	{
		return $this->model->create($data->except('_token', 'page_start_pp'));
	}

	public function deleteEntry($id)
	{
		return $this->model->destroy($id);
	}

	public function max($field)
	{
		return $this->model->max($field);
	}

    public function timezonesArray()
    {
        $timezone_identifiers = \DateTimeZone::listIdentifiers();
        $tz = [];
        foreach ($timezone_identifiers as $k => $v) {
            $tz[$v] = $v;
        }

        return $tz;
    }

    public function countriesArray($show_code = false)
    {
        if ($show_code) {
            return Country::select(DB::raw('id, CONCAT(title, \' (+\', dialing, \')\') AS title'))->orderBy('title', 'ASC')->lists('title', 'id')->all();
        } else {
            return Country::select('id', 'title')->orderBy('title', 'ASC')->lists('title', 'id')->all();
        }
    }

    public function experienceArray()
    {
        $experience = [];

        for ($i = 0; $i <= 25; $i++) {
            $experience[$i] = $i;
        }

        return $experience;
    }

    public function instructorsArray()
    {
        return User::instructors()->select(DB::raw('id, CONCAT(name, \' \', surname, \' (\', email, \')\') AS name'))->orderBy('name', 'ASC')->lists('name', 'id')->all();
    }

    public function membersArray()
    {
        return User::members()->select(DB::raw('id, CONCAT(name, \' \', surname, \' (\', email, \')\') AS name'))->orderBy('name', 'ASC')->lists('name', 'id')->all();
    }

    public function usersArray()
    {
        return User::select(DB::raw('id, CONCAT(name, \' \', surname, \' (\', email, \')\') AS name'))->where('user_type', '<>', 1)->orderBy('name', 'ASC')->lists('name', 'id')->all();
    }

    public function classTypeArray($level = 3, $initial = false, $compound_id = true)
    {
        if ($initial) {
            $ret = [0 => 'No Parent (Primary Category)'];
        } else {
            $ret = [];
        }
        
        $types = ClassType::select('id', 'title', 'parent_id')->orderBy('ordering', 'ASC')->get();

        foreach ($types as $v) {
            if ($v->parent_id == 0) {
                $vid = $compound_id ? $v->id . '_0_0' : $v->id;
                $ret[$vid] = $v->title;
                foreach ($types as $v2) {
                    if ($v2->parent_id == $v->id) {
                        $vid2 = $compound_id ? $v->id . '_' . $v2->id . '_0' : $v2->id;
                        $ret[$vid2] = '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;' . $v2->title;
                        if ($level == 3) {
                            foreach ($types as $v3) {
                                if ($v3->parent_id == $v2->id) {
                                    $vid3 = $compound_id ? $v->id . '_' . $v2->id . '_' . $v3->id : $v3->id;
                                    $ret[$vid3] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;' . $v3->title;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ret;
    }
}
