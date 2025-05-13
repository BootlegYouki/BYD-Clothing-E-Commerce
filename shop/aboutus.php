<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>About Us - Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/logo.ico" type="image/x-icon">
    <!-- UTILITY CSS  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/aboutus.css">
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/assistant.css">
    <link rel="stylesheet" href="css/aboutus.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <!-- CHATBOT  -->
    <?php include 'includes/assistant.php'; ?>
    <!-- SHOPPING CART MODAL  -->
    <?php include 'includes/shopcart.php'; ?>
    <!-- LOGIN/REGISTER MODALS -->
    <?php include 'includes/register.php'; ?>
    <?php include 'includes/login.php'; ?>
    <?php include 'includes/logout.php'; ?>
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <?php include 'includes/terms.php'; ?>
    
    <!-- HERO SECTION -->
    <section class="about-hero">
        <div class="container pt-5">
            <h1>Beyond Doubt Clothing</h1>
            <p class="lead">Learn more about our Store.</p>
        </div>
    </section>
    
    <!-- ABOUT SECTION -->
    <section class="about-section">
    <div class="container px-5">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image">
                    <img src="img/about/about-us-main.jpg" alt="BYD Clothing Team" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6 d-flex flex-column justify-content-center">
                <h2>Beyond Doubt: More Than Just Clothing</h2>
                <br>
                <p class="text-justify">At Beyond Doubt Clothing, we believe in the power of expression through fashion. Founded in 2018, our journey began with a clear vision: to create apparel that helps you showcase your unique identity with confidence. What sets us apart is our unwavering commitment to quality and attention to detail. Each garment we produce is crafted with premium materials and constructed to withstand the test of time, ensuring you not only look good but feel good in what you wear. Our talented team of designers draws inspiration from contemporary trends while adding unique elements that make our pieces distinctively Beyond Doubt. We're not just selling clothes—we're offering you a way to express yourself <em>beyond doubt</em>.</p>
                <div class="about-stats mt-4">
                    <div class="row">
                        <div class="col-4 text-center">
                            <h3>5+</h3>
                            <p>Years of Excellence</p>
                        </div>
                        <div class="col-4 text-center">
                            <h3>1000+</h3>
                            <p>Happy Customers</p>
                        </div>
                        <div class="col-4 text-center">
                            <h3>100%</h3>
                            <p>Quality Commitment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    
    <!-- VALUES SECTION -->
    <section class="about-section bg-light">
        <div class="container px-5">
            <div class="text-center mb-5">
                <h2>Our Core Values</h2>
                <hr class="body-hr mx-auto">
                <p>The principles that guide everything we do</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h4>Quality Excellence</h4>
                        <p>We never compromise on quality. From fabric selection to the final stitch, every detail matters.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <h4>Creative Design</h4>
                        <p>Our designers blend current trends with timeless aesthetics to create pieces that stand out.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Customer Satisfaction</h4>
                        <p>Your happiness is our priority. We're committed to providing an exceptional shopping experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- SUBLIMATION SERVICE SECTION -->
    <section class="about-section">
        <div class="container px-5">
            <div class="row">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <div class="about-image">
                    <img src="img/about/sublimation.jpg" alt="BYD Clothing Team" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h2>Custom Sublimation Service</h2>
                    <br>
                    <p class="text-justify">Bring your vision to life with our premium sublimation printing service. Utilizing state-of-the-art technology and high-grade materials, we offer fully customized apparel that perfectly captures your designs with vibrant, long-lasting colors.</p>
                    <h5>Our Sublimation Services Include:</h5>
                    <ul>
                        <li>T-shirts and casual wear</li>
                        <li>Sports jerseys and athletic apparel</li>
                        <li>Promotional merchandise</li>
                        <li>Team uniforms</li>
                        <li>Event-specific apparel</li>
                    </ul>
                    <p class="text-justify">Whether you're a business looking for branded merchandise, a sports team needing custom uniforms, or an individual with a unique design concept, our sublimation service delivers exceptional quality with quick turnaround times.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- PROCESS SECTION -->
    <section class="about-section bg-light">
        <div class="container px-5">
            <div class="text-center mb-5">
                <h2>Our Process</h2>
                <hr class="body-hr mx-auto">
                <p>From concept to creation, quality at every step</p>
            </div>
            <div class="row">
                <div class="col-md-3 mb-4 text-center">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <h1 class="mb-0">1</h1>
                    </div>
                    <h4 class="mt-3">Design</h4>
                    <p>Our creative team drafts designs that balance style and functionality.</p>
                </div>
                <div class="col-md-3 mb-4 text-center">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <h1 class="mb-0">2</h1>
                    </div>
                    <h4 class="mt-3">Material Selection</h4>
                    <p>We source premium fabrics that offer comfort and durability.</p>
                </div>
                <div class="col-md-3 mb-4 text-center">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <h1 class="mb-0">3</h1>
                    </div>
                    <h4 class="mt-3">Production</h4>
                    <p>Skilled craftspeople bring designs to life with precision.</p>
                </div>
                <div class="col-md-3 mb-4 text-center">
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <h1 class="mb-0">4</h1>
                    </div>
                    <h4 class="mt-3">Quality Check</h4>
                    <p>Every piece undergoes thorough inspection before shipping.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section bg-light">
    <div class="container px-3">
        <div class="text-center mb-5">
            <h2>Connect With Us</h2>
            <hr class="body-hr mx-auto">
            <p>Get custom sublimation quotes and support</p>
        </div>
        
        <div class="social-follow-card mb-4 p-4 bg-white">
    <div class="row align-items-center">
        <div class="col-12">
            <div class="p-4 rounded bg-white h-100 mb-4">
                <h4 class="fw-bold mb-3 text-primary"><i class="fas fa-tshirt me-2"></i>Why Choose Our Sublimation Service?</h4>
                <ul class="sublimation-benefits mb-3 ps-0" style="list-style: none;">
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> <strong>Unmatched Color Brilliance:</strong> Your designs will pop with vibrant, fade-resistant colors that last wash after wash.</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> <strong>Built to Last:</strong> Our prints are guaranteed not to crack, peel, or fade—no matter how often you wear them.</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> <strong>Fully Personalized:</strong> We turn your ideas into reality, offering complete customization for any project or event.</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> <strong>Fast, Friendly Service:</strong> Enjoy quick turnaround times and responsive support from our expert team.</li>
                </ul>
                <div class="alert alert-info mb-0" style="font-size: 0.98em;">
                    Ideal for teams, businesses, events, and personal projects. Discover the difference of professional sublimation—crafted just for you!
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-9">
                    <h4>Message Us For Custom Sublimation</h4>
                    <p class="mb-0">Contact us directly on Facebook for custom sublimation quotes, designs, and support. Follow our page for latest designs, promotions, and behind-the-scenes content.</p>
                </div>
                <div class="col-lg-3 text-md-center text-lg-end text-center mt-3 mt-md-0">
                    <a href="https://www.facebook.com/profile.php?id=100094756167660" target="_blank" class="btn-follow">
                        <i class="fab fa-facebook-f me-2"></i> Message Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<!-- FAQ SECTION -->
<section class="about-section">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2>Frequently Asked Questions</h2>
            <hr class="body-hr mx-auto">
            <p>Everything you need to know about our products and services</p>
        </div>
        
        <div class="row justify-content-center">
            <!-- Left Column -->
            <div class="col-lg-6">
                <div class="accordion faq-accordion" id="faqAccordionLeft">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <i class="fas fa-question-circle me-2 text-primary"></i> How can I place an order?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordionLeft">
                            <div class="accordion-body py-3">
                                To place an order, browse our products, add items to your cart, and proceed to checkout. You'll be guided through the payment and shipping process.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fas fa-ruler me-2 text-primary"></i> What are your size options?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordionLeft">
                            <div class="accordion-body py-3">
                                Sizes vary depending on the product. Each item includes a size chart to help you choose the best fit.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-shipping-fast me-2 text-primary"></i> How long will it take for my order to arrive?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordionLeft">
                            <div class="accordion-body py-3">
                                Shipping times vary depending on your location and the shipping method chosen. Typically, orders are processed within 1-2 business days, and standard shipping takes 5-7 business days.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <i class="fas fa-globe me-2 text-primary"></i> Do you offer international shipping?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordionLeft">
                            <div class="accordion-body py-3">
                                Currently, we do not offer international shipping. Our products are only available for delivery within the Philippines. We are continuously working to expand our reach, so stay tuned for future updates.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="col-lg-6">
                <div class="accordion faq-accordion" id="faqAccordionRight">
                    <!-- FAQ Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <i class="fas fa-credit-card me-2 text-primary"></i> What payment methods do you accept?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordionRight">
                            <div class="accordion-body py-3">
                                We accept major credit/debit cards, GCash, Maya and through QRPh.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 6 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <i class="fas fa-exchange-alt me-2 text-primary"></i> How do I return or exchange an item?
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordionRight">
                            <div class="accordion-body py-3">
                                At the moment, we do not offer returns or exchanges. We encourage you to double-check your order details before confirming your purchase. If you have concerns, feel free to reach out—we're here to help!
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 7 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSeven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                <i class="fas fa-truck-loading me-2 text-primary"></i> How can I track my order?
                            </button>
                        </h2>
                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#faqAccordionRight">
                            <div class="accordion-body py-3">
                                You can track your order by going to Profile > Orders. There, you'll see real-time updates on your order status, such as Processing Payment, Shipped and Delivered. You'll also receive a confirmation message once your order is shipped and Delivered. For any concerns, You can email us at <a href="https://mail.google.com/mail/?view=cm&fs=1&to=beyond.doubtclothing1@gmail.com" target="_blank">beyond.doubtclothing1@gmail.com</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 8 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingEight">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                <i class="fas fa-headset me-2 text-primary"></i> How can I contact customer support?
                            </button>
                        </h2>
                        <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#faqAccordionRight">
                            <div class="accordion-body py-3">
                                If I can't answer your question, You can email us at <a href="https://mail.google.com/mail/?view=cm&fs=1&to=beyond.doubtclothing1@gmail.com" target="_blank">beyond.doubtclothing1@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
    
    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/url-cleaner.js"></script>
    
    <script src="js/shop.js"></script>
</body>
</html>