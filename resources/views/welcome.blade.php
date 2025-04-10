<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>在行云-为独立开发者而生</title>
  <meta name="description"
    content="在行云-为独立开发者提供组件化的云服务组件/模块，帮助独立开发者快速构建自己的APP" />
  <meta name="keywords" content="在行云,独立开发者,个人开发者,APP云组件,APP注册模块,APP支付模块,小程序开发,公众号开发,APP开发,H5开发,OA定制开发,ERP开发,抖店应用开发,Chrome插件开发" />
  <meta name="robots" content="all" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Microsoft YaHei", sans-serif;
        background-color: #f9f9f9;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .container {
        width: 100%;
        flex: 1;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        box-sizing: border-box;
    }
    
    .content-wrapper {
        display: flex;
        width: 100%;
        max-width: 1200px;
        align-items: center;
        justify-content: center;
        gap: 60px;
        flex-wrap: wrap;
    }
    
    .index-img {
        max-width: 60%;
        max-height: 65vh;
        height: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    
    .index-img:hover {
        transform: scale(1.02);
    }
    
    .wechat-container {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        margin: 20px 0;
        transition: all 0.3s ease;
        border: 1px solid #eaeaea;
    }
    
    .wechat-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
    }
    
    .wechat-title {
        margin-bottom: 20px;
        color: #333;
        font-size: 18px;
        font-weight: bold;
    }
    
    .wechat-qrcode {
        width: 200px;
        height: 200px;
        object-fit: contain;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    
    .wechat-qrcode:hover {
        transform: scale(1.05);
    }
    
    footer {
        background-color: #f5f5f5;
        padding: 15px 0;
        border-top: 1px solid #e0e0e0;
        width: 100%;
    }
    
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        text-align: center;
        color: #666;
        font-size: 14px;
    }
    
    .footer-content p {
        margin: 0;
        line-height: 1.6;
    }
    
    .footer-content a {
        color: #3498db;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .footer-content a:hover {
        color: #2980b9;
        text-decoration: underline;
    }
    
    /* 媒体查询：针对小屏幕设备 */
    @media (max-width: 800px) {
        .content-wrapper {
            flex-direction: column;
            gap: 30px;
        }
        
        .index-img {
            max-width: 90%;
        }
        
        .wechat-container {
            width: 85%;
            padding: 20px 15px;
        }
        
        .footer-content {
            padding: 0 15px;
            font-size: 12px;
        }
    }
  </style>
</head>

<body>
    <div class="container">
        <div class="content-wrapper">
            <img src="images/index.png" class="index-img" alt="在行云-为独立开发者而生">
            
            <div class="wechat-container">
                <div class="wechat-title">扫码添加微信，期待您的意见</div>
                <img src="images/wechat.jpg" class="wechat-qrcode" alt="微信二维码">
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-content">
            <p>© 2016 - 2025 孔目湖（北京）科技有限公司 Powered by <a href="https://www.zaihangyun.com/" target="_self">在行云</a>
            <br><a href="https://beian.miit.gov.cn/" target="_blank">京ICP备17023015号-2</a></p>
        </div>
    </footer>
</body>

</html>