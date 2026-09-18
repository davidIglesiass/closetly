<?php

class Utils{

    public static function deleteSession($name){
        if(isset($_SESSION[$name])){
            $_SESSION[$name] = null;
            unset($_SESSION[$name]);
        }
        return $name;
    }

    public static function isAdmin(){
        if(!isset($_SESSION['admin'])){
            header('Location: /');
            exit;
        }
        return true;
    }

    public static function isIdentity(){
        if(!isset($_SESSION['identity'])){
            header('Location: /');
            exit;
        }
        return true;
    }

    public static function isAdminOrSeller(){
        $rol = $_SESSION['identity']['rol'] ?? null;
        if(!in_array($rol, ['admin', 'vendedor'], true)){
            header('Location: /');
            exit;
        }
        return true;
    }

    public static function generateCsrfToken(){
        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken($token){
        if(empty($_SESSION['csrf_token']) || empty($token)) return false;
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitize($data){
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function showCategories(){
        require_once 'models/CategoryRepository.php';
        return (new CategoryRepository())->findAll();
    }

    public static function statsCarShop(){
        $stats = array('count' => 0, 'total' => 0);
        if(isset($_SESSION['carshop'])){
            foreach($_SESSION['carshop'] as $product){
                $stats['count'] += $product['units'];
                $stats['total'] += $product['price'] * $product['units'];
            }
        }
        return $stats;
    }

    public static function showStatus($status){
        $labels = [
            'requested' => 'Requested',
            'paid' => 'Paid',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        return $labels[$status] ?? 'Requested';
    }

    public static function defaultProductImage($categoryName){
        return $categoryName === 'Hoodies' ? '/assets/img/hoodie-black.png' : '/assets/img/tshirt-black.png';
    }

    public static function icon($name){
        $icons = [
            'plus' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'edit' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>',
            'trash' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>',
            'save' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>',
            'eye' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>',
        ];
        return $icons[$name] ?? '';
    }
}