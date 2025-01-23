@extends('admin.layouts.admin') <!-- Подключение общего layout -->

@section('title', 'SEO Записи') <!-- Заголовок страницы -->

@section('content')


    <div class="card">
        <div class="d-flex justify-content-start my-4">
            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary mb-3">Добавить запись</a>
        </div>
        <h5 class="card-header">SEO Записи</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Meta Title</th>
                    <th>Meta Description</th>
                    <th>Header SEO</th>
                    <th>Main SEO</th>
                    <th>Code</th>
                    <th>Изменить</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                @foreach($seoData as $seo)
                    <tr>
                        <td>{{ $seo->id }}</td>
                        <td>{{ $seo->meta_title }}</td>
                        <td>{{ $seo->meta_description }}</td>
                        <td>{{ $seo->header_seo }}</td>
                        <td>{{ $seo->main_seo }}</td>
                        <td>{{ $seo->code }}</td>
                        <td>
                            <a href="{{ route('admin.seo.edit', $seo->id) }}" class="btn btn-primary btn-sm">Изменить</a>
                        </td>
                        <td>
                            <form action="{{ route('admin.seo.destroy', $seo->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
