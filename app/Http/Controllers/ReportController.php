<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->get("type");
        $reports = null;
        switch ($type) {
            case 'comment':
                # code...
                $reports = Report::query()
                    ->with("reportable")
                    ->where("reportable_type", $type)->get();

                break;

            default:
                # code...
                break;
        }

        return $this->success([
            "data" => $reports,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReportRequest $request)
    {
        $reportedId = $request->get("reportedId");
        $morphClass = $request->get("type");
        $arr = $request->only([
            "user_id",
            "message",
        ]);

        $report = new Report($arr);
        $morph = null;
        switch ($morphClass) {
            case 'user':
                $morph = User::find($reportedId);
                # code...
                break;
            case 'comment':
                $morph = Comment::find($reportedId);
                # code...
                break;
            case 'story':
                $morph = Story::find($reportedId);
                break;

            default:
                # code...
                break;
        }
        if (!empty($morph)) {
            $morph->reports()->save($report);
            return $this->success([
                "data" => $report
            ]);
        }
        return $this->failure([
            "message" => "MorphClass not found"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReportRequest  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        //
    }
}
