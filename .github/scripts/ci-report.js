import fs from "fs";
import path from "path";

const input = process.argv[2];
const browser = process.env.BROWSER || "unknown";
const repo = process.env.GITHUB_REPOSITORY || "local";
const runId = process.env.GITHUB_RUN_ID || "0";
const serverUrl = process.env.GITHUB_SERVER_URL || "https://github.com";

if (!input) {
  console.error("Usage: node ci-report.js <results.json> [logs.txt]");
  process.exit(2);
}

const logInput = process.argv[3];

let data;
try {
  data = JSON.parse(fs.readFileSync(input, "utf8"));
} catch (e) {
  console.error("Failed to parse JSON results:", e.message);
  process.exit(1);
}

let md = `# 🧪 E2E Test Report (${browser.toUpperCase()})\n\n`;

md += `> **Workflow:** [${repo}](${serverUrl}/${repo}/actions/runs/${runId}) | **Browser:** \`${browser}\` | **Date:** ${new Date().toUTCString()}\n\n`;

// Summary Table
const statusEmoji = data.numFailedTests > 0 ? "❌" : "✅";
md += `### Summary ${statusEmoji}\n\n`;
md += `| Metric | Value |\n|---|---|\n`;
md += `| **Result** | ${data.numFailedTests > 0 ? "FAILED" : "PASSED"} |\n`;
md += `| **Total Tests** | ${data.numTotalTests} |\n`;
md += `| **Tests Passed** | ${data.numPassedTests || (data.numTotalTests - data.numFailedTests)} |\n`;
md += `| **Tests Failed** | ${data.numFailedTests} |\n`;
md += `| **Suites** | ${data.numTotalTestSuites} |\n`;
md += `| **Duration** | ${(data.startTime ? (Date.now() - data.startTime) / 1000 : "-")}s |\n\n`;

let failed = false;

for (const suite of data.testResults) {
  const suiteFailed = suite.status === "failed" || suite.assertionResults?.some(t => t.status === "failed");
  if (suiteFailed) failed = true;

  md += `### ${suiteFailed ? "❌" : "✅"} ${path.basename(suite.name)}\n\n`;

  if (suite.message) {
    md += `<details open>\n<summary><strong>Suite Error Log</strong></summary>\n\n`;
    md += "```text\n" + suite.message.trim() + "\n```\n";
    md += `</details>\n\n`;

    console.log(`::error title=E2E Suite Failure (${browser})::${suite.message.split("\n")[0]}`);
  }

  if (suite.assertionResults?.length) {
    md += `| Test Case | Status | Time |\n| :--- | :--- | :--- |\n`;

    for (const test of suite.assertionResults) {
      const isFail = test.status === "failed";
      md += `| ${test.fullName} | ${isFail ? "❌ **Failed**" : "✅ Passed"} | ${test.duration ?? "-"}ms |\n`;

      if (isFail) {
        console.log(`::error title=Test Failed (${browser})::${test.fullName}`);
        if (test.failureMessages?.length) {
          md += `\n> **Error Details:**\n> \`\`\`text\n> ${test.failureMessages[0].split('\n')[0]}\n> \`\`\`\n\n`;
        }
      }
    }
    md += `\n`;
  }
}

// Add logs if provided
if (logInput && fs.existsSync(logInput)) {
  const logs = fs.readFileSync(logInput, "utf8");
  md += `## 📜 Application Logs (Last 50 lines)\n\n`;
  md += `<details>\n<summary>Click to expand logs</summary>\n\n`;
  md += "```text\n" + logs.split("\n").slice(-50).join("\n") + "\n```\n";
  md += `</details>\n\n`;
}

/* ===== OUTPUT HANDLING ===== */
const out = process.env.GITHUB_STEP_SUMMARY;
if (out) {
  fs.appendFileSync(out, md);
} else {
  const reportPath = `report-${browser}.md`;
  fs.writeFileSync(reportPath, md);
  console.log(`Report generated: ${reportPath}`);
}

process.exit(failed ? 1 : 0);

