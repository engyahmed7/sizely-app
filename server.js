const { exec } = require("child_process");

// Start Laravel's built-in PHP server
const phpServer = exec(
    "php -S 0.0.0.0:8000 -t public",
    (error, stdout, stderr) => {
        if (error) {
            console.error(`Error starting PHP server: ${error.message}`);
            return;
        }
        console.log(`PHP Server Output: ${stdout}`);
        console.error(`PHP Server Errors: ${stderr}`);
    }
);

// Exit PHP server when Node.js process ends
process.on("exit", () => phpServer.kill());
