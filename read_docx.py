import zipfile
import xml.etree.ElementTree as ET
import sys
import os

def extract_docx_text(docx_path):
    if not os.path.exists(docx_path):
        print(f"File not found: {docx_path}")
        return

    try:
        with zipfile.ZipFile(docx_path, 'r') as zip_ref:
            # Word documents store text in word/document.xml
            with zip_ref.open('word/document.xml') as doc_xml:
                tree = ET.parse(doc_xml)
                root = tree.getroot()
                
                # Namespaces
                ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
                
                # Extract all text elements
                texts = []
                for paragraph in root.iterfind('.//w:p', ns):
                    p_text = ""
                    for run in paragraph.iterfind('.//w:t', ns):
                        if run.text:
                            p_text += run.text
                    if p_text:
                        texts.append(p_text)
                
                print("\n".join(texts))
    except Exception as e:
        print(f"Error reading docx: {e}")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        extract_docx_text(sys.argv[1])
    else:
        print("Usage: python read_docx.py <path_to_docx>")
