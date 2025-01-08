@extends('admin.layouts.admin')

@section('title', 'Редактировать SEO запись')

@section('content')
    <div class="card">
        <h5 class="card-header">Редактировать Product запись</h5>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $products->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="meta_title" name="name" value="{{ old('name', $products->name) }}">
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $products->slug) }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $products->description) }}">
                </div>

                <div class="mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $products->meta_title) }}">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description">{{ old('meta_description', $products->meta_description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $products->code) }}" required>
                </div>

                <button type="submit" class="btn btn-success">Сохранить изменения</button>
            </form>
        </div>
    </div>
@endsection
