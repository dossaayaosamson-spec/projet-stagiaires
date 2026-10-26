import zipfile
import xml.etree.ElementTree as ET
import sys
import os

def extract_text_from_pptx(pptx_path):
    if not os.path.exists(pptx_path):
        print(f"File not found: {pptx_path}")
        return

    text_runs = []
    try:
        with zipfile.ZipFile(pptx_path, 'r') as z:
            for filename in z.namelist():
                if filename.startswith('ppt/slides/slide') and filename.endswith('.xml'):
                    xml_content = z.read(filename)
                    tree = ET.fromstring(xml_content)
                    
                    # XML namespaces used in PPTX
                    namespaces = {
                        'a': 'http://schemas.openxmlformats.org/drawingml/2006/main',
                        'p': 'http://schemas.openxmlformats.org/presentationml/2006/main'
                    }
                    
                    slide_texts = []
                    for node in tree.findall('.//a:t', namespaces):
                        if node.text:
                            slide_texts.append(node.text)
                    
                    if slide_texts:
                        text_runs.append(f"--- {filename} ---")
                        text_runs.append('\n'.join(slide_texts))
    except Exception as e:
        print(f"Error reading pptx: {e}")
        return

    print('\n\n'.join(text_runs))

if __name__ == "__main__":
    pptx_file = sys.argv[1]
    extract_text_from_pptx(pptx_file)
