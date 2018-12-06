const { execSync } = require('child_process');

module.exports = {
    generateInvoice(data) {
        return execSync(`php ${__dirname}/pdf.php "${encodeURIComponent(JSON.stringify(data))}"`);
    },
};
