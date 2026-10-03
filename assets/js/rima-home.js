/* ==========================================================================
   RIMA ACADEMY - HOME MODERN (2026) JS
   ========================================================================== */

document.addEventListener("DOMContentLoaded", () => {
    
    // Register GSAP ScrollTrigger
    if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
        gsap.registerPlugin(ScrollTrigger);

        // Hero Glass Card Enter Animation
        gsap.to(".rhm-hero-content", {
            opacity: 1,
            y: 0,
            duration: 1.2,
            ease: "power4.out",
            delay: 0.2
        });

        // 3. Horizontal Scroll Section - Removed per user request

        // 4. Parallax Backgrounds
        gsap.utils.toArray(".rhm-parallax-bg").forEach(bg => {
            gsap.to(bg, {
                yPercent: 30,
                ease: "none",
                scrollTrigger: {
                    trigger: bg.parentElement,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: true
                }
            });
        });

        // 5. Fade In Elements
        gsap.utils.toArray(".rhm-step, .rhm-course-card, .rhm-trusted-logos span").forEach(item => {
            gsap.from(item, {
                opacity: 0,
                y: 30,
                duration: 0.8,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: item,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                }
            });
        });
    }

    // 6. Magnetic Buttons
    const magneticBtns = document.querySelectorAll(".magnetic-btn");
    magneticBtns.forEach(btn => {
        btn.addEventListener("mousemove", (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            // Move button slightly towards cursor
            gsap.to(btn, {
                x: x * 0.3,
                y: y * 0.3,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        btn.addEventListener("mouseleave", () => {
            gsap.to(btn, {
                x: 0,
                y: 0,
                duration: 0.7,
                ease: "elastic.out(1, 0.3)"
            });
        });
    });

    // 7. Marquee Pause on Hover (Vanilla JS fallback just in case CSS fails, though CSS handles it)
    const marquees = document.querySelectorAll(".rhm-marquee-track");
    marquees.forEach(m => {
        m.addEventListener("mouseenter", () => m.style.animationPlayState = 'paused');
        m.addEventListener("mouseleave", () => m.style.animationPlayState = 'running');
    });

    // 8. 3D Flip Card Interaction
    const flipContainers = document.querySelectorAll(".rhm-flip-container");
    const closeButtons = document.querySelectorAll(".rhm-flip-close");

    flipContainers.forEach(container => {
        const inner = container.querySelector(".rhm-flip-inner");
        const openBtn = container.querySelector(".rhm-flip-open");
        
        // Open card ONLY when explicit button is clicked
        if (openBtn) {
            openBtn.addEventListener("click", function(e) {
                e.stopPropagation();

                // Close all other cards first
                flipContainers.forEach(c => {
                    if (c !== container) {
                        c.classList.remove('is-flipped');
                        c.style.height = ""; // Reset height
                    }
                });

                // Toggle this card
                container.classList.add("is-flipped");
                
                // Expand the card dynamically to fit all courses
                const back = container.querySelector('.rhm-flip-back');
                const courseList = container.querySelector('.rhm-flip-course-list');
                const header = container.querySelector('.rhm-flip-back-header');
                const footer = container.querySelector('.rhm-flip-back-footer');
                
                if (back && courseList) {
                    let totalHeight = 0;
                    if (header) totalHeight += header.offsetHeight;
                    if (footer) totalHeight += footer.offsetHeight;
                    totalHeight += courseList.scrollHeight;
                    
                    // Add buffer for paddings (back padding is ~50px total, plus margins/gaps)
                    totalHeight += 150; 
                    
                    // Enforce a minimum height so it doesn't shrink smaller than default
                    const minHeight = window.innerWidth <= 768 ? 500 : 550;
                    container.style.height = Math.max(minHeight, totalHeight) + "px";
                }
            });
        }
    });

    // Ensure links inside flip cards work (bypassing any 3D event issues or theme JS)
    document.addEventListener("click", function(e) {
        const courseLink = e.target.closest('.rhm-course-list-card');
        if (courseLink) {
            e.preventDefault();
            e.stopPropagation();
            const href = courseLink.getAttribute('href');
            if (href) {
                window.location.href = href;
            }
        }
    }, true); // Capture phase to intercept before anything else

    // Close button logic
    closeButtons.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.stopPropagation(); // prevent triggering the inner click
            const container = this.closest('.rhm-flip-container');
            if (container) {
                container.classList.remove('is-flipped');
                container.style.height = ""; // Reset height to CSS default
            }
        });
    });

});
