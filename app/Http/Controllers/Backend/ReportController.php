<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use DateTime;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function ReportView(){

        return view('admin.backend.report.report_view');

    }
    public function SearchByDate(Request $request){

        $date = new DateTime($request->date);
        $formatDate = $date->format('d F Y');

        $payment = Payment::where('order_date',$formatDate)->latest()->get();
        return view('admin.backend.report.report_by_date',compact('payment','formatDate'));

    }
    public function SearchByMonth(Request $request){

        $month = $request->month;
        $year = $request->year_name;

        $payment = Payment::where('order_month',$month)->where('order_year',$year)->latest()->get();
        return view('admin.backend.report.report_by_month',compact('payment','month','year'));

    }


    public function SearchByYear(Request $request){

        $year = $request->year;

        $payment = Payment::where('order_year',$year)->latest()->get();
        return view('admin.backend.report.report_by_year',compact('payment', 'year'));

    }
}
