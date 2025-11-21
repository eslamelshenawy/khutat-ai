<?php

namespace App\Http\Controllers;

use App\Models\ExportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportTemplateController extends Controller
{
    /**
     * Display a listing of the user's export templates
     */
    public function index()
    {
        $templates = auth()->user()->exportTemplates()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('export-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new export template
     */
    public function create()
    {
        return view('export-templates.create');
    }

    /**
     * Store a newly created export template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'primary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'font_family' => 'required|string',
            'font_size_base' => 'required|integer|min:8|max:20',
            'include_header' => 'boolean',
            'include_footer' => 'boolean',
            'include_page_numbers' => 'boolean',
            'include_table_of_contents' => 'boolean',
            'header_text' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'template_type' => 'required|in:pdf,word,powerpoint,all',
            'is_default' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('export-templates/logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        // If setting as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            auth()->user()->exportTemplates()->update(['is_default' => false]);
        }

        $validated['user_id'] = auth()->id();
        $template = ExportTemplate::create($validated);

        return redirect()->route('export-templates.index')
            ->with('success', 'تم إنشاء قالب التصدير بنجاح');
    }

    /**
     * Show the form for editing the specified export template
     */
    public function edit(ExportTemplate $exportTemplate)
    {
        $this->authorize('update', $exportTemplate);

        return view('export-templates.edit', compact('exportTemplate'));
    }

    /**
     * Update the specified export template
     */
    public function update(Request $request, ExportTemplate $exportTemplate)
    {
        $this->authorize('update', $exportTemplate);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'primary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'font_family' => 'required|string',
            'font_size_base' => 'required|integer|min:8|max:20',
            'include_header' => 'boolean',
            'include_footer' => 'boolean',
            'include_page_numbers' => 'boolean',
            'include_table_of_contents' => 'boolean',
            'header_text' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'template_type' => 'required|in:pdf,word,powerpoint,all',
            'is_default' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($exportTemplate->logo_path) {
                Storage::disk('public')->delete($exportTemplate->logo_path);
            }

            $logoPath = $request->file('logo')->store('export-templates/logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        // If setting as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            auth()->user()->exportTemplates()
                ->where('id', '!=', $exportTemplate->id)
                ->update(['is_default' => false]);
        }

        $exportTemplate->update($validated);

        return redirect()->route('export-templates.index')
            ->with('success', 'تم تحديث قالب التصدير بنجاح');
    }

    /**
     * Remove the specified export template
     */
    public function destroy(ExportTemplate $exportTemplate)
    {
        $this->authorize('delete', $exportTemplate);

        // Delete logo file
        if ($exportTemplate->logo_path) {
            Storage::disk('public')->delete($exportTemplate->logo_path);
        }

        $exportTemplate->delete();

        return redirect()->route('export-templates.index')
            ->with('success', 'تم حذف قالب التصدير بنجاح');
    }

    /**
     * Set a template as the default
     */
    public function setDefault(ExportTemplate $exportTemplate)
    {
        $this->authorize('update', $exportTemplate);

        // Unset all other defaults
        auth()->user()->exportTemplates()->update(['is_default' => false]);

        // Set this one as default
        $exportTemplate->update(['is_default' => true]);

        return redirect()->back()->with('success', 'تم تعيين القالب كافتراضي');
    }
}
