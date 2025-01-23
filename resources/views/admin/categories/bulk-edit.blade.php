@extends('admin.layouts.admin')

@section('title', 'Массовое редактирование SEO')

@section('content')
    <div class="card">
        <h5 class="card-header">Массовое редактирование SEO для категорий</h5>
        <div class="card-body">
            <form action="{{ route('admin.categories.bulk-update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="category_ids" class="form-label">Выберите категории</label>
                    <select class="form-control" id="category_ids" name="category_ids[]" multiple>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description"></textarea>
                </div>

                <div class="mb-3">
                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords">
                </div>

                <div class="mb-3">
                    <label for="h1" class="form-label">H1</label>
                    <input type="text" class="form-control" id="h1" name="h1">
                </div>

                <div class="mb-3">
                    <label for="canonical_url" class="form-label">Canonical URL</label>
                    <input type="text" class="form-control" id="canonical_url" name="canonical_url">
                </div>

                <div class="mb-3">
                    <label for="robots" class="form-label">Robots</label>
                    <input type="text" class="form-control" id="robots" name="robots">
                </div>

                <button type="submit" class="btn btn-success">Сохранить изменения</button>
            </form>
        </div>
    </div>
@endsection
