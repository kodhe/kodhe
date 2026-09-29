<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>404 Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .error-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-number {
            font-size: 120px;
            font-weight: 800;
            color: #667eea;
            line-height: 1;
            margin-bottom: 20px;
            letter-spacing: 5px;
        }

        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .error-message {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f7fafc;
            color: #667eea;
            border: 2px solid #cbd5e0;
        }

        .btn-secondary:hover {
            background: #edf2f7;
            transform: translateY(-2px);
        }

        .icon {
            margin-right: 8px;
            font-size: 18px;
        }

        /* Simple icons using CSS */
        .home-icon::before {
            content: "🏠";
            margin-right: 8px;
        }
        
        .back-icon::before {
            content: "←";
            margin-right: 8px;
            font-weight: bold;
        }

        .error-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
            font-size: 14px;
            color: #555;
            text-align: left;
            border-left: 4px solid #667eea;
        }

        @media (max-width: 600px) {
            .error-card {
                padding: 30px 20px;
            }
            
            .error-number {
                font-size: 80px;
            }
            
            .error-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }

        /* Floating animation for 404 */
        .error-number span {
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        .error-number span:nth-child(2) {
            animation-delay: 0.5s;
        }

        .error-number span:nth-child(3) {
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-number">
            <span>4</span>
            <span>0</span>
            <span>4</span>
        </div>
        
        <h1 class="error-title">Page Not Found</h1>
        
        <div class="error-message">
            <p><?php echo isset($heading) ? $heading : 'The page you are looking for might have been removed or is temporarily unavailable.'; ?></p>
        </div>
        
        <div class="error-actions">
            <a href="<?php echo base_url(); ?>" class="btn btn-primary">
                <span class="home-icon"></span>Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <span class="back-icon"></span>Go Back
            </a>
        </div>
        
        <?php if(isset($message) && !empty($message)): ?>
        <div class="error-details">
            <p><?php echo $message; ?></p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Simple hover effect for buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add a simple click animation
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });
    </script>
</body>
</html>