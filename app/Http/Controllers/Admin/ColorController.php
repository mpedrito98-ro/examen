<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Color;
use Nusagates\Helper\Responses;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $colors = Color::paginate(20);
        return Responses::showSuccessMessage('Success', $colors);
        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'color' => 'required|string|min:3|max:20',
            'base' => 'required|string|min:3|max:20',
        ];
        $validation = Validator::make($request->post(), $rules);
        if ($validation->fails()) {
            return Responses::showValidationError($validation);
        }
        $color = Color::create([
            'color' => $request->color,
            'base' => $request->base,
        ]);
        /*$roles = Role::whereIn('id', $request->roles)->get();
        $color = $color->syncRoles($roles);
        foreach ($roles as $role) {
            $color->syncPermissions($role->permissions);
        }*/
        return Responses::showSuccessMessage('An User has been created', $color);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $color=Color::find($id);
        
        $rules = [
            'color' => 'required|string|min:3|max:20',
            'base' => 'required|string|min:3|max:20',
        ];
        $validation = Validator::make($request->post(), $rules);
        if ($validation->fails()) {
            return Responses::showValidationError($validation);
        }
        $colorData = [
            'color' => $request->color,
            'base' => $request->base,
        ];

        $color->update($colorData);

        return Responses::showSuccessMessage('User has been updated', $color);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $color=Color::find($id);
        $color->delete();
        return Responses::showSuccessMessage('User has been deleted');
    }
}