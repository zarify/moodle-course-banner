// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Move the course banner into the page header.
 *
 * @module     local_coursebanner/banner
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    return {
        init: function(bannerHeight) {
            // Default to 80px if not provided
            bannerHeight = bannerHeight || 80;
            
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    buildBanner(bannerHeight);
                });
            } else {
                buildBanner(bannerHeight);
            }
            
            function buildBanner(height) {
                var bannerImg = document.getElementById('local-coursebanner-img');
                var pageHeader = document.querySelector('#page-header .w-100');
                var contextHeader = document.querySelector('#page-header .page-context-header');
                
                if (!bannerImg || !pageHeader || !contextHeader) {
                    return;
                }
                
                // Check if we're viewing a section (single section view)
                var isSectionView = document.querySelector('.single-section') !== null || 
                                   document.querySelector('#single_section_tiles') !== null;
                
                // Create banner container
                var container = document.createElement('div');
                container.className = 'coursebanner-container';
                container.style.position = 'relative';
                container.style.height = height + 'px';
                container.style.overflow = 'hidden';
                container.style.marginBottom = '10px';
                
                // Style the banner image
                bannerImg.style.display = 'block';
                bannerImg.style.width = '100%';
                bannerImg.style.height = height + 'px';
                bannerImg.style.objectFit = 'cover';
                
                // Create title overlay
                var titleOverlay = document.createElement('div');
                titleOverlay.className = 'coursebanner-title-overlay';
                titleOverlay.style.position = 'absolute';
                titleOverlay.style.top = '50%';
                titleOverlay.style.transform = 'translateY(-50%)';
                titleOverlay.style.left = '50px';
                titleOverlay.style.right = '20px';
                
                // Move context header into overlay
                contextHeader.parentNode.removeChild(contextHeader);
                titleOverlay.appendChild(contextHeader);
                
                // Style the title
                var h1 = contextHeader.querySelector('h1');
                if (h1) {
                    // If viewing a section, replace section name with course name
                    if (isSectionView) {
                        // Get course name from breadcrumb
                        var breadcrumbCourseLink = document.querySelector('.breadcrumb .breadcrumb-item a[title]');
                        if (breadcrumbCourseLink) {
                            var courseName = breadcrumbCourseLink.getAttribute('title');
                            if (courseName) {
                                h1.textContent = courseName;
                            }
                        }
                    }
                    
                    // Apply consistent styling with !important to override theme styles
                    h1.style.setProperty('color', 'white', 'important');
                    h1.style.setProperty('text-shadow', '2px 2px 4px rgba(0,0,0,0.8)', 'important');
                    
                    // Remove theme-specific decorations like underlines
                    // by hiding ::after and ::before pseudo-elements
                    var styleId = 'coursebanner-title-style';
                    if (!document.getElementById(styleId)) {
                        var style = document.createElement('style');
                        style.id = styleId;
                        style.textContent = `
                            .coursebanner-title-overlay h1::after,
                            .coursebanner-title-overlay h1::before,
                            .coursebanner-title-overlay h2::after,
                            .coursebanner-title-overlay h2::before {
                                display: none !important;
                            }
                        `;
                        document.head.appendChild(style);
                    }
                }
                
                // Assemble the structure
                container.appendChild(bannerImg);
                container.appendChild(titleOverlay);
                
                // Insert at top of page header
                pageHeader.insertBefore(container, pageHeader.firstChild);
            }
        }
    };
});
