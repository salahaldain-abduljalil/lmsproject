<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\smtpSetting;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class SettingController extends Controller
{
    public function SmtpSetting()
    {

        $smpt = smtpSetting::find(1);
        return view('admin.backend.setting.smpt_update', compact('smpt'));
    }

    public function SmtpUpdate(Request $request)
    {

        $smtp_id = $request->id;

        SmtpSetting::find($smtp_id)->update([
            'mailer' =>  $request->mailer,
            'host' =>  $request->host,
            'port' =>  $request->port,
            'username' =>  $request->username,
            'password' =>  $request->password,
            'encryption' =>  $request->encryption,
            'from_address' =>  $request->from_address,
        ]);

        $notification = array(
            'message' => 'Smtp Setting Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function SiteSetting()
    {

        $site = SiteSetting::find(1);
        return view('admin.backend.site.site_update', compact('site'));
    }

    public function UpdateSite(Request $request)
    {

        $site_id = $request->id;

        if ($request->file('logo')) {

            $image = $request->file('logo');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(140, 41)->save('upload/logo/' . $name_gen);
            $save_url = 'upload/logo/' . $name_gen;

            SiteSetting::find($site_id)->update([
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'copyright' => $request->copyright,
                'logo' => $save_url,

            ]);

            $notification = array(
                'message' => 'Site Setting Updated with image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {

            SiteSetting::find($site_id)->update([
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'copyright' => $request->copyright,

            ]);

            $notification = array(
                'message' => 'Site Setting Updated without image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } // end else

    }
}
