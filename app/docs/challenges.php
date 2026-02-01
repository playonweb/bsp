<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Markdown Docs</title>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown-dark.min.css">
  <link rel="stylesheet" href="/assets/css/docs.css">
</head>

<body>

  <nav>
    <a href="#" class="brand">📄 Docs</a>
    <div class="links">
      <a href="/">Home</a>
      <a href="/docs/challenges.php" class="highlight">Challenges</a>
      <a href="/docs/solutions.php">Solutions</a>
      <a href="/docs/openapi.php">API</a>
    </div>
  </nav>

  <article class="markdown-body" id="content"></article>

  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script>
    fetch('challenges.md')
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