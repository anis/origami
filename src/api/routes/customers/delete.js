const db = require('../../../database/client');

module.exports = async (req, res) => {
    try {
        await db.query(
            'DELETE FROM customers WHERE customer_id=$1',
            [req.params.id],
        );
        res.status(200).json({
            message: 'The customer has been exterminated.',
        });
    } catch (error) {
        res.status(400).json({
            error: error.message,
        });
    }
};
