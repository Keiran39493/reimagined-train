<?php
// Start the session to manage user login status
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Library - Digital Accessibility</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function copyPrompt(id) {
            const textElement = document.getElementById(id);
            const textToCopy = textElement.dataset.prompt;
            navigator.clipboard.writeText(textToCopy).then(() => {
                alert("Prompt copied to clipboard!");
            }).catch(err => {
                console.error("Could not copy text: ", err);
            });
        }

        function handleLinkClick(event, id) {
            event.preventDefault();
            copyPrompt(id);
            window.open(event.target.href, '_blank');
        }
    </script>
</head>
<body>

<?php include('header.php'); ?>

    <div class="container">
        <main>
            <section class="library">
                <h2>Prompt Library</h2>
                <p><strong>Note:</strong> Clicking on the AI tool links below will automatically copy the prompt to your clipboard.</p>
                <p>Below is a collection of prompts designed to help solve various accessibility issues:</p>
                <ul class="prompt-list">
                    <!-- Prompt 1 -->
                    <li class="prompt-item">
                        <h3>Problem: Images without text alternatives</h3>
                        <p id="prompt1" data-prompt="Describe an image of a person using a laptop in a park. The description should be detailed and cover the surroundings as well."><strong>Prompt:</strong> "Describe an image of a person using a laptop in a park. The description should be detailed and cover the surroundings as well."</p>
                        <button onclick="copyPrompt('prompt1')">Copy Prompt</button>
                        <div class="ai-suggestions">
                            <h4>Generative AI Suggestions:</h4>
                            <ul>
                                <li>
                                    <a href="https://chat.openai.com/" target="_blank" onclick="handleLinkClick(event, 'prompt1')">ChatGPT</a> - <strong>Recommended</strong>
                                    <p>ChatGPT is recommended for generating detailed and contextually accurate descriptions due to its advanced language understanding capabilities.</p>
                                </li>
                                <li>
                                    <a href="https://gemini.google.com/" target="_blank" onclick="handleLinkClick(event, 'prompt1')">Gemini</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Prompt 2 -->
                    <li class="prompt-item">
                        <h3>Problem: Videos without captions</h3>
                        <p id="prompt2" data-prompt="Generate detailed captions for a video where a professor explains the basics of quantum physics in a classroom setting."><strong>Prompt:</strong> "Generate detailed captions for a video where a professor explains the basics of quantum physics in a classroom setting."</p>
                        <button onclick="copyPrompt('prompt2')">Copy Prompt</button>
                        <div class="ai-suggestions">
                            <h4>Generative AI Suggestions:</h4>
                            <ul>
                                <li>
                                    <a href="https://gemini.google.com/" target="_blank" onclick="handleLinkClick(event, 'prompt2')">Gemini</a> - <strong>Recommended</strong>
                                    <p>Gemini excels at understanding and generating detailed captions for educational videos, making it ideal for academic content.</p>
                                </li>
                                <li>
                                    <a href="https://chat.openai.com/" target="_blank" onclick="handleLinkClick(event, 'prompt2')">ChatGPT</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Prompt 3 -->
                    <li class="prompt-item">
                        <h3>Problem: Insufficient color contrast</h3>
                        <p id="prompt3" data-prompt="Suggest a color scheme for a website that ensures a contrast ratio of at least 4.5:1 for text against its background. Provide specific color codes for both text and background."><strong>Prompt:</strong> "Suggest a color scheme for a website that ensures a contrast ratio of at least 4.5:1 for text against its background. Provide specific color codes for both text and background."</p>
                        <button onclick="copyPrompt('prompt3')">Copy Prompt</button>
                        <div class="ai-suggestions">
                            <h4>Generative AI Suggestions:</h4>
                            <ul>
                                <li>
                                    <a href="https://chat.openai.com/" target="_blank" onclick="handleLinkClick(event, 'prompt3')">ChatGPT</a> - <strong>Recommended</strong>
                                    <p>ChatGPT is recommended for generating accessible color schemes due to its ability to understand and implement WCAG guidelines.</p>
                                </li>
                                <li>
                                    <a href="https://gemini.google.com/" target="_blank" onclick="handleLinkClick(event, 'prompt3')">Gemini</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </section>
        </main>
    </div>
    <footer>
        <p>&copy; 2024 Digital Accessibility Project. All Rights Reserved.</p>
    </footer>
</body>
</html>
