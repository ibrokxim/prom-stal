@extends('admin.layouts.admin')

@section('title', 'Добавить категорию')

@section('content')
    <div class="card">
        <h5 class="card-header">Добавить категорию</h5>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Название</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Краткое описание</label>
                    <textarea class="form-control" id="short_description" name="short_description">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description">{{ old('meta_description') }}</textarea>
                </div>

{{--                <div class="mb-3">--}}
{{--                    <label for="code" class="form-label">Код</label>--}}
{{--                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>--}}
{{--                </div>--}}

                <div class="mb-3">
                    <label for="picture" class="form-label">Изображение</label>
                    <input type="file" class="form-control" id="picture" name="picture">
                </div>

                <button type="submit" class="btn btn-success">Добавить категорию</button>
            </form>
        </div>
    </div>
@endsection
