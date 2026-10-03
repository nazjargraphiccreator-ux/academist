<!-- 3. HOW IT WORKS (The RIMA Pathway) -->
    <section class="rhm-how-it-works" id="how-it-works">
        <div class="rhm-container">
            <div class="rhm-section-header">
                <h2 class="rhm-h2">The <span class="rhm-cyan">RIMA</span> Pathway</h2>
                <p class="rhm-p">A proven step-by-step method to achieve fluency and global certification.</p>
            </div>
            
            <div class="rhm-interactive-pathway">
                <!-- Navigation Tabs -->
                <div class="rhm-pathway-nav">
                    <button class="rhm-pathway-tab active" data-target="pane-1">
                        <span class="step-num">01</span>
                        <span class="step-title">Placement & Assessment</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-2">
                        <span class="step-num">02</span>
                        <span class="step-title">Choose Your Course</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-3">
                        <span class="step-num">03</span>
                        <span class="step-title">Live Native Tutors</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-4">
                        <span class="step-num">04</span>
                        <span class="step-title">Practice & Assignments</span>
                    </button>
                    <button class="rhm-pathway-tab" data-target="pane-5">
                        <span class="step-num">05</span>
                        <span class="step-title">Get Your Certificate</span>
                    </button>
                </div>
                
                <!-- Content Panes -->
                <div class="rhm-pathway-content">
                    
                    <div class="rhm-pathway-pane active" id="pane-1">
                        <h3>Placement & Assessment</h3>
                        <p>Start your journey with a quick assessment. We evaluate your current fluency level to ensure you are placed in the perfect group for maximum growth.</p>
                        <ul>
                            <li>Free initial assessment</li>
                            <li>Personalized learning roadmap</li>
                            <li>Accurate CEFR level placement</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-2">
                        <h3>Choose Your Course</h3>
                        <p>Browse our catalog of expert-curated courses ranging from absolute beginner (A1) to mastery (C2) based on your personal or professional goals.</p>
                        <ul>
                            <li>General Language Courses</li>
                            <li>Business & Corporate Training</li>
                            <li>Exam Preparation (IELTS, JLPT, etc.)</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-3">
                        <h3>Live Native Tutors</h3>
                        <p>Join interactive live sessions directly from our platform. Our certified native tutors guide you through immersive conversations and real-world scenarios.</p>
                        <ul>
                            <li>Interactive Zoom integration</li>
                            <li>Small group or 1-on-1 sessions</li>
                            <li>Learn authentic pronunciation</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-4">
                        <h3>Practice & Assignments</h3>
                        <p>Solidify your knowledge outside of class. Complete interactive quizzes, submit homework, and track your progress in real-time through your student dashboard.</p>
                        <ul>
                            <li>Automated progress tracking</li>
                            <li>Interactive quizzes & exams</li>
                            <li>Detailed feedback from tutors</li>
                        </ul>
                    </div>

                    <div class="rhm-pathway-pane" id="pane-5">
                        <h3>Get Your Certificate</h3>
                        <p>Successfully complete your course modules and pass the final assessment to receive your official certificate of completion from Rima Academy.</p>
                        <ul>
                            <li>Verified Certificate of Completion</li>
                            <li>Add directly to your CV/LinkedIn</li>
                            <li>Proof of your language proficiency</li>
                        </ul>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const tabs = document.querySelectorAll('.rhm-pathway-tab');
                    const panes = document.querySelectorAll('.rhm-pathway-pane');

                    tabs.forEach(tab => {
                        tab.addEventListener('click', () => {
                            // Remove active class from all
                            tabs.forEach(t => t.classList.remove('active'));
                            panes.forEach(p => p.classList.remove('active'));

                            // Add active to clicked
                            tab.classList.add('active');
                            const targetId = tab.getAttribute('data-target');
                            document.getElementById(targetId).classList.add('active');
                        });
                    });
                });
            </script>
        </div>
    </section>

    
