<?php

namespace App\Http\Controllers;

use App\Models\RateStory;
use App\Http\Requests\StoreRateStoryRequest;
use App\Http\Requests\UpdateRateStoryRequest;
use App\Models\Story;
use App\Traits\ResponseTrait;

class RateStoryController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreRateStoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRateStoryRequest $request, Story $story)
    {
        $arr = $request->validated();
        $rateStory = new RateStory($arr);
        $rateStory->story_id = $story->id;
        $rateStory->save();
        return $this->success([
            'data' => $rateStory->only([
                'characteristic',
                'plot',
                'world_building',
                'quality_convert'
            ])
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RateStory  $rateStory
     * @return \Illuminate\Http\Response
     */
    public function show(RateStory $rateStory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RateStory  $rateStory
     * @return \Illuminate\Http\Response
     */
    public function edit(RateStory $rateStory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRateStoryRequest  $request
     * @param  \App\Models\RateStory  $rateStory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRateStoryRequest $request, RateStory $rateStory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RateStory  $rateStory
     * @return \Illuminate\Http\Response
     */
    public function destroy(RateStory $rateStory)
    {
        //
    }
}
