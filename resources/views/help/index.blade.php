@extends('layouts.app')

@section('title', 'Help & Support')
@section('page-title', 'Help & Support')
@section('page-description', 'Get help with using the M-right Digital Receipt System')

@push('styles')
<style>
.accordion-body ol li {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}

.accordion-body strong {
    color: #0d8abc;
    font-weight: 600;
}

.accordion-button:not(.collapsed) {
    background-color: rgba(13, 138, 188, 0.1);
    border-color: rgba(13, 138, 188, 0.2);
    color: #0d8abc;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 138, 188, 0.25);
}

.card.border-success:hover,
.card.border-primary:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.accordion-body {
    font-size: 0.95rem;
    line-height: 1.7;
}

.accordion-button {
    font-weight: 500;
    font-size: 1rem;
}

.support-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.support-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.support-icon {
    transition: transform 0.3s ease;
}

.support-card:hover .support-icon {
    transform: scale(1.1);
}

.faq-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2rem;
    margin-top: 2rem;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1.5rem;
    border-bottom: 3px solid #0d8abc;
    padding-bottom: 0.5rem;
    display: inline-block;
}

@media (max-width: 768px) {
    .support-card {
        margin-bottom: 1rem;
    }
    
    .faq-section {
        padding: 1.5rem;
        margin: 1rem 0;
    }
    
    .accordion-body {
        font-size: 0.9rem;
    }
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>Help & Support Center
                </h5>
                <small class="opacity-75">Get assistance with M-right Digital Receipt System</small>
            </div>
            <div class="card-body">
                
                <!-- Support Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary support-card">
                            <div class="card-body text-center">
                                <i class="fas fa-book text-primary mb-3 support-icon" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold">Getting Started Guide</h5>
                                <p class="text-muted">Learn how to set up your account and start generating receipts.</p>
                                <a href="#faqAccordion" class="btn btn-primary">
                                    <i class="fas fa-arrow-down me-2"></i>Read Guide
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success support-card">
                            <div class="card-body text-center">
                                <i class="fas fa-headset text-success mb-3 support-icon" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold">Contact Support</h5>
                                <p class="text-muted">Get in touch with our support team for assistance.</p>
                                <a href="mailto:adapolyproject@gmail.com" class="btn btn-success">
                                    <i class="fas fa-envelope me-2"></i>Email Support
                                </a>
                                <small class="d-block text-muted mt-2">
                                    <i class="fas fa-at me-1"></i>adapolyproject@gmail.com
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="faq-section">
                    <h6 class="section-title">
                        <i class="fas fa-question-circle me-2"></i>Frequently Asked Questions
                    </h6>
                    <p class="text-muted mb-4">Find answers to common questions about M-right Digital Receipt System</p>
                    
                    <div class="accordion" id="faqAccordion">
                        
                        <!-- FAQ 1: Digital Receipt Payment -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="fas fa-credit-card me-2 text-primary"></i>
                                    By Digital Receipt, are the Customers pay for phone they buy Online?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <strong>No!</strong><br><br>
                                    Customers who buy Phone MUST pay you in cash or transfer the money to your bank account. No changes in the mode of payment or collection for all your sales.<br><br>
                                    But you can add minimum of one thousand Naira (₦1,000) to the actual cost of the phone as Digital Receipt charges. then pay six hundred naira online to M-right while generating the digital Receipt at the site.<br><br>
                                    <div class="alert alert-info">
                                        <strong>Example:</strong> If the price of the phone is ₦60,000, you can collect ₦61,000 from the Customer either as cash or transfer to your Bank account. While generating the Digital Receipt at the M-right portal, you are to pay six hundred Naira (₦600) from your account. As soon as you finish generating the Receipt, the System automatically sends the copy of the Receipt to the Customer's email account you typed while filling the online form. By that, both Your sales money and receipt generation commission are with you.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2: Commission -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                    How do I get my receipt generation commission?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Your commission is already with you since you are collecting the Digital Receipt charges along with the phone money directly from the Customer before even paying the ₦600 for the Receipt to M-right from your Bank account.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3: Sales Records -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="fas fa-chart-bar me-2 text-warning"></i>
                                    Why I'm seeing all my sales record despite collecting all my sales revenue directly from the Customers?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    The major reason why we are keeping this records is that, in case of any disaster, even though we are not praying for it, it allows AMPAT to know your capital strength along with the number of Receipts you generated to know how much they will give you as assistance. The assistance they give you are coming from the revenue they get from generating the receipt. Therefore higher the number of Receipts you generated, higher the assistance you get. Your sales capital strength also contributes to what you will get.<br><br>
                                    Secondly, this record serves as tool for your internal accounting and analysis. They are just figures. Nothing M-right takes from it nor M-right remitting for you. Your sales revenue is already in your hand. Your sales capital strength also determines what you will get.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4: Benefits for Customers -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <i class="fas fa-user-shield me-2 text-info"></i>
                                    What are the benefits of M-right Digital Receipt to Customers?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li><strong>First Layer Security:</strong> The digital receipt is a first layer of phone security, ensuring that nobody can sell a stolen phone. This is because the resale code is known only to the owner who generated the receipt.</li>
                                        
                                        <li><strong>Always Available Online:</strong> The digital receipt is always available online and can be retrieved using the phone's serial number. This eliminates challenges associated with loss, fire, flood, or delayed access as in paper receipts.</li>
                                        
                                        <li><strong>Anti-Theft System Access:</strong> Generating the receipt also gives access to add the phone to the M-Right Anti-Theft system, thereby providing an additional and stronger layer of security such as downloading and installing of Antitheft apps, to declare phone missing if stolen, to announce missing phone Nationwide, recoverability effort support etc</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5: Benefits for Shop Owners -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    <i class="fas fa-store me-2 text-primary"></i>
                                    What are the benefits of M-right Digital Receipt to Shop Owners?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li><strong>Additional Income:</strong> It creates minimum additional income of ₦400 per receipt generated.</li>
                                        
                                        <li><strong>Customer Confidence:</strong> It restores customer confidence by providing enhanced phone security.</li>
                                        
                                        <li><strong>Silent Insurance:</strong> It serves as silent insurance for Shop Owners as the digital receipt provides revenue for AMPAT to cushion her members in times of disaster. For example, the recent fire incident at Farm Center, Kano and the rampant phone stealing cases in the country are some of the reasons for such win win invention.</li>
                                        
                                        <li><strong>Nationwide Access:</strong> It enables all Shop Owners nationwide to come onboard.</li>
                                        
                                        <li><strong>Business Recognition:</strong> It enhances the business' visibility and recognition nationwide.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6: Phones Already in Use -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    <i class="fas fa-mobile-alt me-2 text-secondary"></i>
                                    Can I generate Digital Receipt for phones already in use?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <div class="alert alert-success">
                                        <strong>Yes.</strong>
                                    </div>
                                    But you have to verify the genuineness of the phone very well with the help of valid ID Card, the pack and the receipt of the phone etc to ensure that it's legitimate owner generating the receipt. Otherwise you are bearing risk that may track down trouble unto you.<br><br>
                                    <div class="alert alert-warning">
                                        <strong>Important:</strong> Another reason for generating receipt for old phone in-use is, without the Digital Receipt, the Owner cannot add it to M-right Antitheft System that gives additional security.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 7: Next Steps -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                    <i class="fas fa-arrow-right me-2 text-success"></i>
                                    What next after generating the Receipt?
                                </button>
                            </h2>
                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                   Customers who want additional Security can add their phones to M-right Antitheft in www.mright.com.ng or in M-right booths available in all Markets or areas close to the Phone shops. Shop owners can also create M-right account for the Customers and add their phone at the cost of ₦1,500<br><br>
                                    
                                    After adding the phone, you have to validate it with validation credit or pay online directly. The validation credit is available from all AMPAT Executives in your Markets at ₦1,000 only. Meaning, in every phone added to the M-right Antitheft, the Shop owner gets ₦500 or more profit. May be you can even charge the Customer more for installation of the App, guide on SIM Passwording etc.<br><br>
                                    
                                    <div class="alert alert-danger">
                                        <strong>Important Reminder:</strong> Always tell the Customers to keep their Receipt resale code safe and remembered. Without it, they cannot add their phones to the Antitheft or resale the phone to somebody.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-primary border-0" style="background: linear-gradient(135deg, #0d8abc, #4dabf7); color: white;">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">
                                        <i class="fas fa-question-circle me-2"></i>Still need help?
                                    </h6>
                                    <p class="mb-0 opacity-75">Our support team is here to assist you with any questions or issues.</p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <a href="mailto:adapolyproject@gmail.com" class="btn btn-light">
                                        <i class="fas fa-envelope me-2"></i>Contact Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to FAQ section when clicking "Read Guide"
    const readGuideBtn = document.querySelector('a[href="#faqAccordion"]');
    if (readGuideBtn) {
        readGuideBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const faqSection = document.getElementById('faqAccordion');
            if (faqSection) {
                faqSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }
    
    // Add animation when accordion items are opened
    const accordionButtons = document.querySelectorAll('.accordion-button');
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            setTimeout(() => {
                const targetId = this.getAttribute('data-bs-target');
                const targetElement = document.querySelector(targetId);
                if (targetElement && targetElement.classList.contains('show')) {
                    targetElement.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }, 350); // Wait for Bootstrap animation to complete
        });
    });
});
</script>
@endpush