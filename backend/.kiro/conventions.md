# Project Conventions

## Controller Rules
- Never use `compact()` to pass variables to views
- Always use `$this->share([...])` to pass data to views
- Use `'model'` as the key name for the main model being edited
- Example:
  ```php
  public function getUpdate($id)
  {
      return view('product.form', $this->share([
          'model' => Product::findOrFail($id)
      ]));
  }
  ```

## Action Classes
- All validation, find, and saving logic belongs in Action classes (app/Actions/)
- Controllers should be thin - only routing, calling actions, flash messages, and redirects
- Use `Lorisleiva\Actions\Concerns\AsAction` trait

## Blade Components
- Use `<x-input>`, `<x-select>`, `<x-textarea>`, etc. (not `<x-form-input>`)
- Use `@bind($model)` / `@endbind` for auto-binding model values to form components
- Use `<x-card label="...">` for card wrappers
- Use `<x-form action="...">` which includes @csrf automatically
- Use `<x-action cancel="/path">` for fixed bottom action bar

## Template / UI
- Based on FlyonUI (DaisyUI fork) with shadcn theme
- Use template/index.html as reference for table pages
- Use template/form.html as reference for form pages
- Icons: tabler icons via `icon-[tabler--name]` class
- Mobile-first responsive design with `lg:` breakpoint for desktop
