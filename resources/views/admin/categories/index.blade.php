@extends('admin.layouts.admin')

@section('title', 'Добавить SEO запись')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Категории</h5>
            <a href="{{ route('admin.categories.bulk-edit') }}" class="btn btn-primary">Массовое редактирование</a>
        </div>
        <div class="d-flex justify-content-start my-4">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Добавить категорию </a>
        </div>
        <h5 class="card-header">Категории</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Meta Title</th>
                    <th>Meta Description</th>
                    <th>Code</th>
                    <th>Изменить</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->meta_title }}</td>
                        <td>{{ $category->meta_description }}</td>
                        <td>{{ $category->code }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">Изменить</a>
                        </td>
                        <td>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            {{ $categories->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

