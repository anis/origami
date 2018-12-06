const db = require('../../../database/client');

module.exports = async (req, res) => {
    try {
        const result = await db.query(
            'SELECT * FROM customers WHERE customer_id=$1',
            [req.params.id],
        );
        res.status(200).json({
            data: result.rows[0],
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
