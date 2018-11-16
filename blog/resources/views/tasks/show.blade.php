@extends('layouts.master')

@section('content')

<h1>{{ $task->body }}</h1>
@if($task->completed)
    <p class="text-success">Complete</p>
@else
<p class="text-danger">Incomplete</p>
@endif

@endsection