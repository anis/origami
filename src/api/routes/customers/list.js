const db = require('../../../database/client');

module.exports = async (req, res) => {
    try {
        const result = await db.query('SELECT * FROM customers');
        res.status(200).json({
            data: result.rows,
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
