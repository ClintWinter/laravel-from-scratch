@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-12">

        <h1 class="display-1">All Tasks</h1>

        <ul class="list-group">
            @foreach ($tasks as $task)
                <li class="list-group-item">
                    @if ($task->completed)
                        <s><a href="/tasks/{{$task->id}}">{{ $task->body }}</a></s>
                    @else
                        <a href="/tasks/{{$task->id}}">{{ $task->body }}</a>
                    @endif
                </li>
            @endforeach
        </ul>


    </div>
<div>

@endsection