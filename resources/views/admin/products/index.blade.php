@extends('admin.layouts.admin')

@section('title', 'Добавить SEO запись')

@section('content')
    <div class="card">
        <div class="d-flex justify-content-start my-4">
            <a href="{{ route('admin.seo.create') }}" class="btn btn-lg btn-primary">Добавить запись</a>
        </div>
        <h5 class="card-header">Product Записи</h5>
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
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->meta_title }}</td>
                        <td>{{ $product->meta_description }}</td>
                        <td>{{ $product->code }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">Изменить</a>
                        </td>
                        <td>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
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
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection
