@extends('admin.layouts.admin')

@section('title', 'Добавить SEO запись')

@section('content')
    <div class="card">
        <h5 class="card-header">Добавление новой SEO записи</h5>
        <div class="card-body">
            <form action="{{ route('admin.seo.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="meta_title" class="form-label">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title">
                </div>
                <div class="mb-3">
                    <label for="meta_description" class="form-label">Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="header_seo" class="form-label">Header SEO</label>
                    <input type="text" class="form-control" id="header_seo" name="header_seo">
                </div>
                <div class="mb-3">
                    <label for="main_seo" class="form-label">Main SEO</label>
                    <textarea class="form-control" id="main_seo" name="main_seo"></textarea>
                </div>
                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                </div>
                <button type="submit" class="btn btn-success">Сохранить</button>
            </form>
        </div>
    </div>
@endsection
