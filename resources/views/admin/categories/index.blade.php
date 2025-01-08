@extends('admin.layouts.admin')

@section('title', 'Добавить SEO запись')

@section('content')
    <div class="card">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Добавить запись</a>
        <h5 class="card-header">Product Записи</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
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
                        <td></td>
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
            {{ $categories->links() }}
        </div>
    </div>
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', () => {--}}
{{--            // Функция для обновления индексов--}}
{{--            const updateTableIndexes = () => {--}}
{{--                const table = document.querySelector('table tbody'); // Ищем тело таблицы--}}
{{--                if (!table) return;--}}

{{--                const rows = table.querySelectorAll('tr'); // Находим все строки таблицы--}}
{{--                rows.forEach((row, index) => {--}}
{{--                    const idCell = row.querySelector('td:first-child'); // Первая ячейка строки--}}
{{--                    if (idCell) {--}}
{{--                        idCell.textContent = index + 1; // Обновляем ID начиная с 1--}}
{{--                    }--}}
{{--                });--}}
{{--            };--}}

{{--            // Обновляем индексы при загрузке страницы--}}
{{--            updateTableIndexes();--}}

{{--            // Пример: вешаем событие на кнопки "Удалить"--}}
{{--            document.querySelectorAll('form button[type="submit"]').forEach((button) => {--}}
{{--                button.addEventListener('click', (event) => {--}}
{{--                    event.preventDefault(); // Отключаем отправку формы--}}
{{--                    const row = button.closest('tr'); // Находим строку с кнопкой--}}
{{--                    if (row) {--}}
{{--                        row.remove(); // Удаляем строку--}}
{{--                        updateTableIndexes(); // Обновляем индексы--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endsection

