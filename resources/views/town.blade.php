@extends('layouts.app')

@section('title', 'Town Builder')

@section('content')
<div class="w-full">
    <div class="w-full bg-black rounded-lg overflow-hidden shadow-xl" style="height: calc(100vh - 180px);">
        <iframe 
            src="{{ asset('brusave_city_builder_v3/City_Builder_Game_ori.html') }}"
            class="w-full h-full border-0"
            allow="fullscreen"
            id="gameFrame"
            title="Town Builder Game"
        ></iframe>
    </div>
</div>
@endsection