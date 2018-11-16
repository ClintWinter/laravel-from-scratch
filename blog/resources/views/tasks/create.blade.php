@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-12">
        <h1>New Task</h1>

        <hr>

        <div class="d-flex justify-content-center">
            <form method="POST" action="/tasks" class="col-6 bg-light p-3 rounded">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="body">Body</label>
                    <input type="text" name="body" class="form-control">
                </div>

                <div class="form-group">
                    <label for="completed">Completed</label>
                    <input type="checkbox" name="completed" value="1" class="form-control">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection