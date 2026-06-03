<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\AttendanceBill;
use App\Models\AttendanceCheckout;
use App\Models\AttendanceClinical;
use App\Models\AttendanceLab;
use App\Models\AttendanceMedical;
use App\Models\AttendanceMovement;
use App\Models\AttendancePayment;
use App\Models\Diagnosis;
use App\Models\Expense;
use App\Models\ExpenseTransaction;
use App\Models\InSession;
use App\Models\Insurance;
use App\Models\InsurancePrice;
use App\Models\LabTest;
use App\Models\Logs;
use App\Models\Medical;
use App\Models\MedicalStock;
use App\Models\Office;
use App\Models\Patient;
use App\Models\PatientAttendance;
use App\Models\PatientVital;
use App\Models\PaymentBreak;
use App\Models\Procedure;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Ward;
use App\Models\WardCheck;
use App\Models\Widget;

class FinancialController extends Controller {


    function __construct() {
    }

    public function expense_ledger_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'financial/expense_ledger/get'
        );
        $data = array(
            'text_string' => '<a class="btn btn-primary" href="'.url('financial/expense_ledger/edit/0').'">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'Name',
                    'Amount',
                    'Created',
                    'Option',
                )
            )
        );
        $_title = 'Expense Ledger';
        $_sub_title = '';
        $_page_index = 'financial';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function expense_ledger_index_get()
    {
        $l = DB::table('expenses');

        $l->select('id','name','amount','created_at');
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('financial/expense_ledger/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
             ->editColumn('created_at', function($row) {
            return date('H:i, d M Y',strtotime($row->created_at));
        })
            ->removeColumn('id')
            ->toJson();
    }

    public function expense_ledger_edit($id = 0)
    {

        $expense = Expense::find($id);
        $data = [];
        $data['expense'] = $expense;

        $_title = 'Edit Expense Ledger';
        if( is_null($expense) ) {
            $_title = 'Create New Expense';
        }

        $_sub_title = '<i class="icon-bar-chart"></i>';
        $_page_index = 'financial';
        return view('manager.expense_ledger.edit', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    }

    public function expense_ledger_save($id = 0)
    {
        $expense = Expense::find($id);
        if( is_null($expense) ) {
            $expense = new Expense();
            $expense->creator_id = Auth::user()->id;
            $expense->amount = 0;
        }

        $expense->name = request('name');
        $expense->updator_id = Auth::user()->id;
        $expense->save();

        return Redirect::to('financial/expense_ledger')
            ->with('success','Expense ledger saved!');

    }


    public function expense_transaction_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'financial/expense_transaction/get'
        );
        $data = array(
            'text_string' => '<a class="btn btn-primary" href="'.url('financial/expense_transaction/edit/0').'"> Add New </a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'Time',
                    'User',
                    'Expense',
                    'Remark',
                    'Amount',
                )
            )
        );
        $_title = 'Expense Transactions';
        $_sub_title = '';
        $_page_index = 'financial';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function expense_transaction_index_get()
    {
        $l = DB::table('expense_transaction_lists');

        $l->select('id','created_at', 'creator', 'name','remark','amount');
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('financial/expense_ledger/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->editColumn('created_at', function($row) {
            return date('H:i, d M Y',strtotime($row->created_at));
        })
            ->removeColumn('id')
            ->toJson();
    }

    public function expense_transaction_edit($id = 0)
    {

        $transaction = ExpenseTransaction::find($id);
        $data = [];
        $data['transaction'] = $transaction;

        $_title = 'Edit Expense Transaction';
        if( is_null($transaction) ) {
            $_title = 'Create New Expense Transaction';
        }
        $_sub_title = '<i class="icon-bar-chart"></i>';
        $_page_index = 'financial';
        return view('manager.expense_ledger.transaction', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    }

    public function expense_transaction_save($id = 0)
    {

//        return request()->all();

        $expenseTransaction = ExpenseTransaction::find($id);
        if( is_null($expenseTransaction) ) {
            $expenseTransaction = new ExpenseTransaction();
            $expenseTransaction->creator_id = Auth::user()->id;
        }

        $expenseTransaction->expense_id = request('expense_id');
        $expenseTransaction->remark = request('remark');
        $expenseTransaction->amount = request('amount');
//        $expenseTransaction->updator_id = Auth::user()->id;
        $expenseTransaction->save();

        $expense = $expenseTransaction->expense;
        $expense->amount = $expense->amount + $expenseTransaction->amount;
        $expense->save();

        return Redirect::to('financial/expense_transaction')
            ->with('success','Expense transaction saved!');

    }

    public function expense_report()
    {

        $from = request('from',date('Y-m-d',strtotime("last month")));
        $to = request('to',date('Y-m-d'));

        $expenseTransactions = ExpenseTransaction::whereRaw('date(created_at) >= ?',[$from])
            ->whereRaw('date(created_at) <= ?',[$to])
            ->orderby('created_at','desc')
            ->get();

        $amount = [];
        $expenseIDs = [];
        $expenses = [];

        foreach ($expenseTransactions as $expenseTransaction) {

            if( in_array($expenseTransaction->expense_id,$expenseIDs) ) {
                $amount[ $expenseTransaction->expense->id ] += $expenseTransaction->amount;
            }
            else {
                $amount[ $expenseTransaction->expense->id ] = 0;
                $amount[ $expenseTransaction->expense->id ] += $expenseTransaction->amount;
                $expenseIDs[] = $expenseTransaction->expense_id;
                $expenses[ $expenseTransaction->expense_id ] = $expenseTransaction->expense;
            }

        }

        $expenseTransactions1 = [];

        foreach ($expenseIDs as $expenseID ) {
            $expense = $expenses[ $expenseID ];
            $expense->total = $amount[ $expenseID ];
            $expenseTransactions1[] = $expense;
        }

        $data['amount'] = $amount;
        $data['transactions'] = $expenseTransactions;
        $data['transactions1'] = $expenseTransactions1;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['title'] = 'Expense Report';

        $_title = 'Expense Report';
        $_sub_title = '<i class="icon-bar-chart"></i>';
        $_page_index = 'report';
        return view('manager.expense_report', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    }


    public function invoice_index()
    {

        $from = request('from',date('Y-m-d',strtotime("last month")));
        $to = request('to',date('Y-m-d'));

        $insurances = Insurance::all();

        $data = [];

        foreach ($insurances as $insurance) {

            $tempData = DB::table('attendance_payment_data')
                ->whereRaw('date(created_at) >= ?',[$from])
                ->whereRaw('date(created_at) <= ?',[$to])
                ->whereRaw('insurance_id = ?',[$insurance->id])
                ->orderby('created_at','asc')
                ->get();

            $insurance->payments = $tempData;

            $data['insurances'][] = $insurance;

        }

        $data['from'] = $from;
        $data['to'] = $to;
//        return $data;

        $_title = 'Insurance Invoice Bill Generator';
        $_sub_title = '<i class="icon-bar-chart"></i>';
        $_page_index = 'financial';
        return view('manager.invoice_generator', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    }



}