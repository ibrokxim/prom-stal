<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seo;

class AdminController extends Controller
{
    public function seoIndex()
    {
        $seoData = Seo::all(); // Получение всех записей
        return view('admin.seo.index', compact('seoData'));
    }


    public function seoCreate()
    {
        return view('admin.seo.create');
    }

    public function seoEdit($id)
    {
        $seo = Seo::findOrFail($id);
        return view('admin.seo.edit', compact('seo'));
    }

    public function seoDestroy($id)
    {
        $seo = Seo::findOrFail($id);
        $seo->delete();

        return redirect()->route('admin.seo.index')->with('success', 'SEO запись удалена!');
    }

    public function seoUpdate(Request $request, $id)
    {
        // Находим SEO запись по ID
        $seo = Seo::findOrFail($id);

        // Валидация данных
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'header_seo' => 'nullable|string|max:255',
            'main_seo' => 'nullable|string',
            'code' => 'required|string|max:255|unique:seo,code,' . $seo->id,
        ]);

        // Обновление записи
        $seo->update($request->all());

        // Перенаправление с сообщением об успехе
        return redirect()->route('admin.seo.index')->with('success', 'SEO запись обновлена!');
    }

    public function seoStore(Request $request)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'header_seo' => 'nullable|string|max:255',
            'main_seo' => 'nullable|string',
            'code' => 'required|string|unique:seo,code|max:255',
        ]);

        Seo::create($request->all());

        return redirect()->route('admin.seo.index')->with('success', 'SEO запись добавлена!');
    }
}

