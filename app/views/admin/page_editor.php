<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar { background: var(--bg); border-color: var(--border) !important; border-top-left-radius: 8px; border-top-right-radius: 8px; }
    .ql-container { background: var(--bg); border-color: var(--border) !important; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; color: var(--text); font-family: 'Inter', sans-serif; font-size: 1rem; }
    .ql-editor { min-height: 400px; }
    .ql-stroke { stroke: var(--text) !important; }
    .ql-fill { fill: var(--text) !important; }
    .ql-picker { color: var(--text) !important; }
</style>

<div class="card">
    <form action="/admin/pages/save" method="POST" id="pageForm">
        <input type="hidden" name="id" value="<?= $page['id'] ?? '' ?>">
        
        <label>Page Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required placeholder="e.g., Privacy Policy">
        
        <label>Slug (URL) - Leave empty to auto-generate</label>
        <input type="text" name="slug" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" placeholder="e.g., privacy-policy">
        
        <label>Content</label>
        <!-- Editor Container -->
        <div id="editor">
            <?= $page['content'] ?? '' // No htmlspecialchars here, we trust the DB content or empty ?>
        </div>
        <!-- Hidden input to store HTML -->
        <input type="hidden" name="content" id="contentInput">
        
        <div style="margin-top: 20px;">
            <button type="submit" class="btn">Save Page</button>
            <a href="/admin/pages" class="btn" style="background: transparent; border: 1px solid var(--border); margin-left: 10px;">Cancel</a>
        </div>
    </form>
</div>

<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    const form = document.getElementById('pageForm');
    form.onsubmit = function() {
        // Populate hidden input with editor HTML
        var content = document.querySelector('input[name=content]');
        content.value = quill.root.innerHTML;
    };
</script>
