<!-- 2. COURSE CATALOG (3D FLIP CARDS) -->
    <section class="rhm-courses" id="courses">
        <div class="rhm-container">
            <div class="rhm-section-header">
                <h2 class="rhm-h2">Choose Your <span class="rhm-cyan">Language</span>.</h2>
                <p class="rhm-p">Click on a language card to reveal the available course levels.</p>
            </div>
            
            
            <div class="rhm-flip-grid">
                <?php if(!empty($language_data)): foreach($language_data as $lang): ?>
                
                <div class="rhm-flip-container">
                    <div class="rhm-flip-inner">
                        
                        <!-- FRONT OF CARD (Language) -->
                        <div class="rhm-flip-front" style="background-image: url('<?php echo esc_url($lang['img']); ?>');">
                            <div class="rhm-flip-front-overlay"></div>
                            
                            <?php 
                                $slug = strtolower($lang['slug']);
                                $stampClass = 'rhm-stamp';
                                $stampText = '';
                                if (in_array($slug, array('romanian', 'romana'))) {
                                    $stampText = 'For Foreigners';
                                } elseif (in_array($slug, array('english'))) {
                                    $stampText = 'Most Popular';
                                    $stampClass .= ' rhm-stamp-blue';
                                } elseif (in_array($slug, array('japanese'))) {
                                    $stampText = 'Trending';
                                    $stampClass .= ' rhm-stamp-purple';
                                }
                            ?>
                            <?php if ($stampText): ?>
                            <div class="<?php echo esc_attr($stampClass); ?>"><?php echo esc_html($stampText); ?></div>
                            <?php endif; ?>

                            <div class="rhm-flip-front-content">
                                <h3><?php echo esc_html($lang['name']); ?></h3>
                                <div class="rhm-flip-hint-wrapper">
                                    <button class="rhm-flip-open" aria-label="View Courses">
                                        <span>View Courses</span>
                                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- BACK OF CARD (Courses) -->
                        <div class="rhm-flip-back" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(2, 6, 23, 0.95) 100%), url('<?php echo esc_url($lang['img']); ?>'); background-size: cover; background-position: center;">
                            <div class="rhm-flip-back-header">
                                <h3>
                                    <?php echo esc_html($lang['name']); ?> Courses
                                    <?php if ($stampText): ?>
                                        <div class="<?php echo esc_attr($stampClass); ?> rhm-stamp-back"><?php echo esc_html($stampText); ?></div>
                                    <?php endif; ?>
                                </h3>
                                <button class="rhm-flip-close" aria-label="Close" title="Back to Languages">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            
                            <div class="rhm-flip-course-list">
                                <?php foreach($lang['courses'] as $course): ?>
                                <a href="<?php echo esc_url($course['link']); ?>" class="rhm-course-list-card" onclick="window.location.href='<?php echo esc_url($course['link']); ?>'; return false;">
                                    <div class="rhm-clc-left">
                                        <div class="rhm-clc-level"><?php echo esc_html($course['level']); ?></div>
                                        <div class="rhm-clc-details">
                                            <p class="rhm-clc-excerpt"><?php echo esc_html($course['excerpt']); ?></p>
                                        </div>
                                    </div>
                                    <div class="rhm-clc-right">
                                        <div class="rhm-clc-price"><?php echo wp_kses_post($course['price_html']); ?></div>
                                        <div class="rima-btn rima-btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">View</div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="rhm-flip-back-footer">
                                <a href="/courses/" class="rhm-flip-view-all">View all <?php echo esc_html($lang['name']); ?> courses &rarr;</a>
                            </div>
                        </div>

                    </div>
                </div>
                
                <?php endforeach; endif; ?>
            </div>

        </div>
    </section>

    
