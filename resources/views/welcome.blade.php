<form action="/import/stores" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="csv_file">Выберите CSV файл:</label>
    <input type="file" name="csv_file" id="csv_file" required>
    <button type="submit">Загрузить и импортировать</button>
</form>
