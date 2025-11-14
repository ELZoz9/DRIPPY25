import 'dart:async';

class ApiStub {
  // Simulate login
  static Future<Map<String, dynamic>> login(String email, String password) async {
    await Future.delayed(Duration(seconds: 1)); // fake API delay
    return {
      "status": "success",
      "user": {
        "id": 1,
        "name": "Ziad Ahmed",
        "email": email,
      }
    };
  }

  // Simulate fetching product list
  static Future<List<Map<String, dynamic>>> getProducts() async {
    await Future.delayed(Duration(seconds: 1));
    return [
      {
        "id": 1,
        "name": "Oversized T-Shirt",
        "brand": "Local Brand A",
        "price": 350,
        "sizes": ["S", "M", "L", "XL"],
        "image": "assets/images/tshirt.png"
      },
      {
        "id": 2,
        "name": "Hoodie",
        "brand": "Local Brand B",
        "price": 650,
        "sizes": ["M", "L"],
        "image": "assets/images/hoodie.png"
      }
    ];
  }

  // Simulate fetching single product details
  static Future<Map<String, dynamic>> getProductDetails(int id) async {
    await Future.delayed(Duration(milliseconds: 500));
    return {
      "id": id,
      "name": "Oversized T-Shirt",
      "brand": "Local Brand A",
      "price": 350,
      "description": "High-quality cotton T-shirt perfect for daily wear.",
      "sizes": ["S", "M", "L", "XL"],
    };
  }

  // Simulate placing an order
  static Future<Map<String, dynamic>> placeOrder(Map<String, dynamic> order) async {
    await Future.delayed(Duration(seconds: 1));
    return {
      "status": "success",
      "order_id": 12345,
      "message": "Order placed successfully."
    };
  }
}
