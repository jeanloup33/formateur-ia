#!/usr/bin/env node
/**
 * Lance un serveur statique puis exécute Lighthouse sur index.html.
 * Génère un rapport HTML + JSON dans le dossier reports/.
 */
const { spawn } = require("child_process");
const fs = require("fs");
const path = require("path");
const httpServer = require("http-server");

const ROOT = path.resolve(__dirname, "..");
const REPORT_DIR = path.join(ROOT, "reports");
const PORT = process.env.LH_PORT || 4173;
const TARGET = `http://127.0.0.1:${PORT}/index.html`;

if (!fs.existsSync(REPORT_DIR)) {
  fs.mkdirSync(REPORT_DIR, { recursive: true });
}

const server = httpServer.createServer({ root: ROOT, cache: 0 });

server.listen(PORT, "127.0.0.1", () => {
  console.log(`> HTTP server prêt sur ${TARGET}`);
  const lighthouseArgs = [
    TARGET,
    "--quiet",
    "--chrome-flags=\"--headless --no-sandbox\"",
    "--output=html",
    "--output=json",
    `--output-path=${path.join(REPORT_DIR, "lighthouse-report")}`,
  ];

  const child = spawn(
    path.join("node_modules", ".bin", "lighthouse"),
    lighthouseArgs,
    { stdio: "inherit", shell: true, cwd: ROOT }
  );

  child.on("exit", (code) => {
    server.close(() => process.exit(code));
  });

  child.on("error", (err) => {
    console.error("Erreur Lighthouse:", err);
    server.close(() => process.exit(1));
  });
});
