<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/csp.php';
?>

<!DOCTYPE html>
<!--
OpenAPIViewer
Source: https://gist.github.com/rahuldhole/3b943093745455f569e9656601a2b672
Usage:
    - add link to your openapi file in `SwaggerUIBundle` url
    - Update topbar links (Link1, Link2) if needed
-->
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>API Documentation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div id="swagger-ui"></div>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        const CustomHeaderPlugin = function (system) {
            return {
                wrapComponents: {
                    Logo: (Original, system) => (props) => {
                        const React = system.React;
                        return React.createElement("div", { style: { display: "flex", alignItems: "center", gap: "20px" } },
                            React.createElement("img", {
                                src: "/favicon.ico",
                                style: { height: "40px", width: "auto" },
                                alt: "Logo"
                            }),
                            React.createElement("div", { style: { display: "flex", gap: "15px" } },
                                React.createElement("a", { href: "/", style: { color: "#fff", textDecoration: "none", fontSize: "14px", fontWeight: "600" } }, "Home"),
                                React.createElement("a", { href: "/docs/index.php", style: { color: "#fff", textDecoration: "none", fontSize: "14px", fontWeight: "600" } }, "Docs")
                            )
                        );
                    }
                }
            };
        };
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: "openapi.yaml",
                dom_id: "#swagger-ui",
                deepLinking: true,
                presets: [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
                plugins: [SwaggerUIBundle.plugins.DownloadUrl, CustomHeaderPlugin],
                layout: "StandaloneLayout",
                defaultModelsExpandDepth: 1,
                docExpansion: "list"
            });
        };
    </script>
</body>

</html>