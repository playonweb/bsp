<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Markdown Docs</title>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown-dark.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#0d1117] min-h-screen m-0 font-sans text-white">

  <nav
    class="flex items-center justify-between px-8 py-4 bg-[#0d1117]/80 backdrop-blur-md border-b border-[#30363d] sticky top-0 z-50">
    <a href="#" class="text-[#e6edf3] text-lg font-semibold no-underline">📄 Docs</a>
    <div class="flex gap-2">
      <a href="/"
        class="text-[#8b949e] text-sm px-3 py-1.5 rounded-md hover:text-[#e6edf3] hover:bg-[#161b22] hover:border-[#30363d] transition-all border border-transparent">Home</a>
      <a href="/docs/challenges.php"
        class="text-[#8b949e] text-sm px-3 py-1.5 rounded-md hover:text-[#e6edf3] hover:bg-[#161b22] hover:border-[#30363d] transition-all border border-transparent">Challenges</a>
      <a href="/docs/solutions.php"
        class="text-[#388bfd] bg-[#1f6feb11] border-[#1f6feb33] text-sm px-3 py-1.5 rounded-md transition-all border">Solutions</a>
      <a href="/docs/openapi.php"
        class="text-[#8b949e] text-sm px-3 py-1.5 rounded-md hover:text-[#e6edf3] hover:bg-[#161b22] hover:border-[#30363d] transition-all border border-transparent">API</a>
    </div>
  </nav>

  <article class="markdown-body max-w-3xl mx-auto px-8 py-10 pb-20" id="content"></article>

  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script>
    fetch('solutions.md')
      .then(res => {
        if (!res.ok) throw new Error('File not found');
        return res.text();
      })
      .then(md => {
        document.getElementById('content').innerHTML = marked.parse(md);
      })
      .catch(err => {
        document.getElementById('content').innerHTML =
          `<p style="color:#f85149">⚠️ ${err.message}</p>`;
      });
  </script>
</body>

</html>