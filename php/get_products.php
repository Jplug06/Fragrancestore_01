<?php
require_once 'config.php';

setJSONHeaders();

// Enhanced fallback products with images and better organization
$fallbackProducts = [
    // Featured Products
    ['id' => 1, 'name' => 'Asad', 'brand' => 'Lattafa', 'subcategory' => 'Spicy/Intense', 'category' => 'Masculine', 'base_price' => 250.00, 'final_price' => 300.00, 'description' => 'Bold and intense spicy fragrance with warm amber and spices. Perfect for confident men who want to make a statement.', 'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 'stock_quantity' => 25, 'featured' => 1, 'rating' => 4.8, 'size' => '100ml'],
    ['id' => 2, 'name' => 'Supremacy Not Only Intense', 'brand' => 'Afnan', 'subcategory' => 'Spicy/Intense', 'category' => 'Masculine', 'base_price' => 300.00, 'final_price' => 350.00, 'description' => 'Powerful and commanding fragrance with intense spices and woody undertones. A true masterpiece for evening wear.', 'image_url' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 'stock_quantity' => 20, 'featured' => 1, 'rating' => 4.7, 'size' => '100ml'],
    ['id' => 6, 'name' => 'Badee Oud For Glory', 'brand' => 'Lattafa', 'subcategory' => 'Oud/Woody', 'category' => 'Masculine', 'base_price' => 320.00, 'final_price' => 370.00, 'description' => 'Luxurious oud fragrance with rich woody undertones and precious saffron. A true Middle Eastern treasure.', 'image_url' => 'https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop', 'stock_quantity' => 12, 'featured' => 1, 'rating' => 4.9, 'size' => '100ml'],
    ['id' => 8, 'name' => 'Khamrah Gahwa', 'brand' => 'Lattafa', 'subcategory' => 'Sweet/Warm', 'category' => 'Masculine', 'base_price' => 350.00, 'final_price' => 400.00, 'description' => 'Sweet coffee-inspired fragrance with warm spices and vanilla. Gourmand masterpiece for coffee lovers.', 'image_url' => 'https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=400&h=400&fit=crop', 'stock_quantity' => 10, 'featured' => 1, 'rating' => 4.9, 'size' => '100ml'],
    ['id' => 10, 'name' => 'Sharaf Blend', 'brand' => 'Lattafa', 'subcategory' => 'Sensual/Evening', 'category' => 'Sexy', 'base_price' => 310.00, 'final_price' => 360.00, 'description' => 'Sensual blend perfect for romantic evenings. Mysterious and captivating with floral and woody notes.', 'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 'stock_quantity' => 18, 'featured' => 1, 'rating' => 4.7, 'size' => '100ml'],
    ['id' => 15, 'name' => 'Khamrah', 'brand' => 'Lattafa', 'subcategory' => 'Gourmand', 'category' => 'Sexy', 'base_price' => 320.00, 'final_price' => 370.00, 'description' => 'Sweet gourmand with irresistible appeal. Rich vanilla, caramel, and warm spices create pure temptation.', 'image_url' => 'https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop', 'stock_quantity' => 15, 'featured' => 1, 'rating' => 4.8, 'size' => '100ml'],
    ['id' => 17, 'name' => 'Maahir Legacy Silver', 'brand' => 'Lattafa', 'subcategory' => 'Aquatic/Clean', 'category' => 'Fresh', 'base_price' => 250.00, 'final_price' => 300.00, 'description' => 'Clean aquatic fragrance with silver notes and fresh marine breeze. Perfect for daily wear.', 'image_url' => 'https://images.unsplash.com/photo-1557170334-a9632e77c6e4?w=400&h=400&fit=crop', 'stock_quantity' => 25, 'featured' => 1, 'rating' => 4.6, 'size' => '100ml'],
    ['id' => 21, 'name' => 'Rasasi Hawas', 'brand' => 'Rasasi', 'subcategory' => 'Citrus/Modern', 'category' => 'Fresh', 'base_price' => 300.00, 'final_price' => 350.00, 'description' => 'Fresh citrus with modern sophistication. Premium quality with excellent longevity.', 'image_url' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 'stock_quantity' => 18, 'featured' => 1, 'rating' => 4.7, 'size' => '100ml'],
    ['id' => 24, 'name' => 'Armaf Iconic', 'brand' => 'Armaf', 'subcategory' => 'Versatile Daily', 'category' => 'Fresh', 'base_price' => 230.00, 'final_price' => 280.00, 'description' => 'Iconic versatile fragrance perfect for daily wear. Fresh, clean, and universally appealing.', 'image_url' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 'stock_quantity' => 35, 'featured' => 1, 'rating' => 4.5, 'size' => '100ml'],

    // Regular Products
    ['id' => 3, 'name' => 'Supremacy Noir', 'brand' => 'Afnan', 'subcategory' => 'Spicy/Intense', 'category' => 'Masculine', 'base_price' => 280.00, 'final_price' => 330.00, 'description' => 'Dark and mysterious composition with black pepper, cardamom, and smoky woods.', 'image_url' => 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=400&h=400&fit=crop', 'stock_quantity' => 18, 'featured' => 0, 'rating' => 4.6, 'size' => '100ml'],
    ['id' => 4, 'name' => 'His Confession', 'brand' => 'Maison Alhambra', 'subcategory' => 'Spicy/Intense', 'category' => 'Masculine', 'base_price' => 230.00, 'final_price' => 280.00, 'description' => 'Confident and bold spicy scent with cinnamon, nutmeg, and warm vanilla.', 'image_url' => 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 'stock_quantity' => 22, 'featured' => 0, 'rating' => 4.5, 'size' => '100ml'],
    ['id' => 5, 'name' => 'Sharof The Club', 'brand' => 'Lattafa', 'subcategory' => 'Spicy/Intense', 'category' => 'Masculine', 'base_price' => 270.00, 'final_price' => 320.00, 'description' => 'Exclusive club-worthy fragrance with premium spices and luxurious woods.', 'image_url' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 'stock_quantity' => 15, 'featured' => 0, 'rating' => 4.7, 'size' => '100ml'],
    ['id' => 7, 'name' => 'Armaf Club De Nuit', 'brand' => 'Armaf', 'subcategory' => 'Oud/Woody', 'category' => 'Masculine', 'base_price' => 300.00, 'final_price' => 350.00, 'description' => 'Sophisticated woody fragrance perfect for evening occasions.', 'image_url' => 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 'stock_quantity' => 16, 'featured' => 0, 'rating' => 4.6, 'size' => '100ml'],
    ['id' => 9, 'name' => 'Supremacy Collector', 'brand' => 'Afnan', 'subcategory' => 'Sweet/Warm', 'category' => 'Masculine', 'base_price' => 290.00, 'final_price' => 340.00, 'description' => 'Collectible sweet and warm composition with honey, vanilla, and warm spices.', 'image_url' => 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 'stock_quantity' => 14, 'featured' => 0, 'rating' => 4.5, 'size' => '100ml'],
    ['id' => 11, 'name' => '9pm', 'brand' => 'Afnan', 'subcategory' => 'Sensual/Evening', 'category' => 'Sexy', 'base_price' => 270.00, 'final_price' => 320.00, 'description' => 'Seductive fragrance designed for night time adventures.', 'image_url' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 'stock_quantity' => 20, 'featured' => 0, 'rating' => 4.6, 'size' => '100ml'],
    ['id' => 12, 'name' => 'Rayhaan Elixir', 'brand' => 'Lattafa', 'subcategory' => 'Sensual/Evening', 'category' => 'Sexy', 'base_price' => 250.00, 'final_price' => 300.00, 'description' => 'Magical elixir of seduction with exotic florals and warm amber.', 'image_url' => 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=400&h=400&fit=crop', 'stock_quantity' => 22, 'featured' => 0, 'rating' => 4.5, 'size' => '100ml'],
    ['id' => 13, 'name' => 'The Kingdom', 'brand' => 'Afnan', 'subcategory' => 'Sensual/Evening', 'category' => 'Sexy', 'base_price' => 260.00, 'final_price' => 310.00, 'description' => 'Royal and commanding evening scent with regal presence.', 'image_url' => 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 'stock_quantity' => 16, 'featured' => 0, 'rating' => 4.6, 'size' => '100ml'],
    ['id' => 14, 'name' => 'Liquid Brun', 'brand' => 'Maison Alhambra', 'subcategory' => 'Sensual/Evening', 'category' => 'Sexy', 'base_price' => 240.00, 'final_price' => 290.00, 'description' => 'Smooth and sensual liquid fragrance with creamy textures.', 'image_url' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 'stock_quantity' => 24, 'featured' => 0, 'rating' => 4.4, 'size' => '100ml'],
    ['id' => 16, 'name' => 'Bourbon', 'brand' => 'Maison Alhambra', 'subcategory' => 'Gourmand', 'category' => 'Sexy', 'base_price' => 260.00, 'final_price' => 310.00, 'description' => 'Rich bourbon-inspired gourmand scent with whiskey notes and sweet vanilla.', 'image_url' => 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 'stock_quantity' => 18, 'featured' => 0, 'rating' => 4.5, 'size' => '100ml'],
    ['id' => 18, 'name' => 'Amber Oud Aqua', 'brand' => 'Maison Alhambra', 'subcategory' => 'Aquatic/Clean', 'category' => 'Fresh', 'base_price' => 280.00, 'final_price' => 330.00, 'description' => 'Unique blend of aquatic freshness with warm amber.', 'image_url' => 'https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=400&h=400&fit=crop', 'stock_quantity' => 20, 'featured' => 0, 'rating' => 4.5, 'size' => '100ml'],
    ['id' => 19, 'name' => 'Afnan 9am Dive', 'brand' => 'Afnan', 'subcategory' => 'Aquatic/Clean', 'category' => 'Fresh', 'base_price' => 270.00, 'final_price' => 320.00, 'description' => 'Fresh morning dive into aquatic notes with energizing citrus.', 'image_url' => 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 'stock_quantity' => 22, 'featured' => 0, 'rating' => 4.4, 'size' => '100ml'],
    ['id' => 20, 'name' => 'Najdia', 'brand' => 'Lattafa', 'subcategory' => 'Citrus/Modern', 'category' => 'Fresh', 'base_price' => 220.00, 'final_price' => 270.00, 'description' => 'Modern citrus fragrance with contemporary appeal.', 'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 'stock_quantity' => 30, 'featured' => 0, 'rating' => 4.3, 'size' => '100ml'],
    ['id' => 22, 'name' => 'Odyssey Mega', 'brand' => 'Armaf', 'subcategory' => 'Citrus/Modern', 'category' => 'Fresh', 'base_price' => 230.00, 'final_price' => 280.00, 'description' => 'Epic citrus journey with modern woods and fresh herbs.', 'image_url' => 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=400&h=400&fit=crop', 'stock_quantity' => 25, 'featured' => 0, 'rating' => 4.4, 'size' => '100ml'],
    ['id' => 23, 'name' => 'Rave Now Intense', 'brand' => 'Lattafa', 'subcategory' => 'Citrus/Modern', 'category' => 'Fresh', 'base_price' => 210.00, 'final_price' => 260.00, 'description' => 'Intense modern citrus for active lifestyle.', 'image_url' => 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 'stock_quantity' => 28, 'featured' => 0, 'rating' => 4.3, 'size' => '100ml'],
    ['id' => 25, 'name' => 'Fakhar Black', 'brand' => 'Lattafa', 'subcategory' => 'Versatile Daily', 'category' => 'Fresh', 'base_price' => 240.00, 'final_price' => 290.00, 'description' => 'Sophisticated daily wear fragrance with modern elegance.', 'image_url' => 'https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop', 'stock_quantity' => 30, 'featured' => 0, 'rating' => 4.4, 'size' => '100ml']
];

try {
    logError("Starting get_products.php");
    
    // Try to get database connection
    $pdo = getDBConnection();
    
    if ($pdo) {
        logError("Database connection successful");
        
        // Try to get products from database
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE stock_quantity > 0 ORDER BY featured DESC, rating DESC, category, subcategory, name");
            $stmt->execute();
            $products = $stmt->fetchAll();
            
            if (count($products) > 0) {
                logError("Found " . count($products) . " products in database");
                sendJSONResponse([
                    'success' => true,
                    'products' => $products,
                    'source' => 'database',
                    'count' => count($products)
                ]);
            } else {
                logError("No products found in database, using fallback");
                sendJSONResponse([
                    'success' => true,
                    'products' => $fallbackProducts,
                    'source' => 'fallback',
                    'count' => count($fallbackProducts),
                    'message' => 'Using fallback products - database is empty'
                ]);
            }
        } catch (Exception $e) {
            logError("Database query failed: " . $e->getMessage());
            sendJSONResponse([
                'success' => true,
                'products' => $fallbackProducts,
                'source' => 'fallback',
                'count' => count($fallbackProducts),
                'message' => 'Database query failed, using fallback products'
            ]);
        }
    } else {
        logError("No database connection, using fallback products");
        sendJSONResponse([
            'success' => true,
            'products' => $fallbackProducts,
            'source' => 'fallback',
            'count' => count($fallbackProducts),
            'message' => 'No database connection, using fallback products'
        ]);
    }
    
} catch (Exception $e) {
    logError("Error in get_products.php: " . $e->getMessage());
    sendJSONResponse([
        'success' => true,
        'products' => $fallbackProducts,
        'source' => 'fallback',
        'count' => count($fallbackProducts),
        'message' => 'Error occurred, using fallback products'
    ]);
}
?>
