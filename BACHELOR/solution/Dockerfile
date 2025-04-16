FROM python:3.12-slim

WORKDIR /app

RUN pip install --no-cache-dir httpx requests

COPY solution.py .

CMD ["python3", "./solution.py"]

